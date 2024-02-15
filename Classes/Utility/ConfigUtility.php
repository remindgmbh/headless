<?php

declare(strict_types=1);

namespace Remind\Headless\Utility;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigUtility
{
    public static function getRootPageConfig(): array
    {
        /** @var \TYPO3\CMS\Core\Site\Entity\Site $site */
        $site = self::getRequest()->getAttribute('site');
        $rootPageId = $site->getRootPageId();

        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('pages');
        $flexFormStr = $queryBuilder
            ->select('tx_headless_config')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($rootPageId, Connection::PARAM_INT)
                )
            )
            ->executeQuery()
            ->fetchOne();

        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        return $flexFormService->convertFlexFormContentToArray($flexFormStr);
    }

    private static function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
