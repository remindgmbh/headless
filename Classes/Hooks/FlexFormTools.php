<?php

declare(strict_types=1);

namespace Remind\Headless\Hooks;

use PDO;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FlexFormTools
{
    public function getDataStructureIdentifierPreProcess(
        array $fieldTca,
        string $tableName,
        string $fieldName,
        array &$row
    ): array {
        if ($tableName === 'tx_headless_item') {
            [$foreignTable, $foreignField] = GeneralUtility::trimExplode(
                ':',
                $fieldTca['config']['ds_tableField'],
                true
            );
            $pointerField = $fieldTca['config']['ds_pointerField'];
            $request = $this->getRequest();
            $uid = $row[$pointerField] ?? null;
            $type = null;
            if ($uid) {
                $isNew = !is_int($uid) && str_starts_with($uid, 'NEW');
                if ($isNew) {
                    $body = $request->getParsedBody();
                    $context = json_decode($body['ajax']['context'] ?? null, true);
                    $config = json_decode($context['config'] ?? null, true);
                    $queryParams = parse_url($config['originalReturnUrl'], PHP_URL_QUERY);
                    parse_str($queryParams, $queryParams);
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
                            $queryBuilder->createNamedParameter($uid, PDO::PARAM_INT)
                        ));

                    $type = $queryBuilder->execute()->fetchOne();
                }
            } else {
                $body = $request->getParsedBody();
                $foreignRow = current($body['data'][$foreignTable]);
                $type = $foreignRow[$foreignField];
            }
            $row[$pointerField] = $type;
        }
        return [];
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
