<?php

declare(strict_types=1);

namespace Remind\Headless\Event\Listener;

use Doctrine\DBAL\ParameterType;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Configuration\Event\AfterFlexFormDataStructureIdentifierInitializedEvent;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AfterFlexFormDataStructureIdentifierInitializedEventListener
{
    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }

    public function __invoke(AfterFlexFormDataStructureIdentifierInitializedEvent $event): void
    {
        if ($event->getTableName() === 'tx_headless_item') {
            $row = $event->getRow();

            $foreignUidField = 'foreign_uid';
            $foreignTable = 'tt_content';
            $foreignField = 'CType';

            $request = $this->getRequest();
            $foreignUid = $row[$foreignUidField] ?? null;
            $type = null;
            if ($foreignUid) {
                $isNew = !is_int($foreignUid) && str_starts_with($foreignUid, 'NEW');
                if ($isNew) {
                    $body = (array) $request->getParsedBody();
                    $context = json_decode($body['ajax']['context'] ?? null, true);
                    $config = json_decode($context['config'] ?? null, true);
                    $queryParams = parse_url($config['originalReturnUrl'], PHP_URL_QUERY);
                    parse_str($queryParams ?: '', $queryParams);
                    $type = $queryParams['defVals'][$foreignTable][$foreignField] ?? null;
                } else {
                    $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
                    $queryBuilder = $connectionPool->getQueryBuilderForTable($foreignTable);
                    $queryBuilder->getRestrictions()
                        ->removeAll()
                        ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
                    $queryBuilder
                        ->select($foreignField)
                        ->from($foreignTable)
                        ->where($queryBuilder->expr()->eq(
                            'uid',
                            $queryBuilder->createNamedParameter($foreignUid, ParameterType::INTEGER)
                        ));

                    $type = $queryBuilder->executeQuery()->fetchOne();
                }
            } else {
                $body = (array) $request->getParsedBody();
                $foreignRow = current($body['data'][$foreignTable]);
                $type = $foreignRow[$foreignField];
            }

            if (isset($GLOBALS['TCA']['tx_headless_item']['columns']['flexform']['config']['ds'][$type])) {
                $identifier = $event->getIdentifier();
                $identifier['dataStructureKey'] = $type;
                $event->setIdentifier($identifier);
            }
        }
    }
}
