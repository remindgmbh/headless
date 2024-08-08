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
    /**
     * @return mixed[]
     */
    public static function getRootPageConfig(): array
    {
        $request = self::getRequest();

        /** @var \TYPO3\CMS\Core\Site\Entity\Site $site */
        $site = $request->getAttribute('site');
        $rootPageId = $site->getRootPageId();

        /** @var \TYPO3\CMS\Core\Site\Entity\SiteLanguage $siteLanguage */
        $siteLanguage = $request->getAttribute('language');

        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('pages');
        $flexFormStr = $queryBuilder
            ->select('tx_headless_config')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->or(
                        $queryBuilder->expr()->eq(
                            'uid',
                            $queryBuilder->createNamedParameter($rootPageId, Connection::PARAM_INT)
                        ),
                        $queryBuilder->expr()->eq(
                            'l10n_parent',
                            $queryBuilder->createNamedParameter($rootPageId, Connection::PARAM_INT)
                        ),
                    ),
                    $queryBuilder->expr()->eq(
                        'sys_language_uid',
                        $queryBuilder->createNamedParameter($siteLanguage->getLanguageId(), Connection::PARAM_INT)
                    ),
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
