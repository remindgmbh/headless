<?php

declare(strict_types=1);

namespace Remind\Headless\Utility;

use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigUtility
{
    public static function getConfig(): array
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

    /**
     * @param array|string $dataStructure either a xml flexform file path, a xml flexform string or a flexform array
     */
    public static function addFlexForm(array|string $dataStructure): void
    {
        $newFlexFormArray = self::getFlexFormArray($dataStructure);
        $currentFlexFormArray = self::getFlexFormArray(
            $GLOBALS['TCA']['pages']['columns']['tx_headless_config']['config']['ds']['default']
        );

        if (($currentFlexFormArray['ROOT']['el'] ?? null) === '') {
            $currentFlexFormArray = [];
        }

        ArrayUtility::mergeRecursiveWithOverrule($currentFlexFormArray, $newFlexFormArray);
        $flexFormTools = GeneralUtility::makeInstance(FlexFormTools::class);
        $newFlexFormString = $flexFormTools->flexArray2Xml($currentFlexFormArray, true);

        $GLOBALS['TCA']['pages']['columns']['tx_headless_config']['config']['ds']['default'] = $newFlexFormString;
    }

    private static function getFlexFormArray(array|string $dataStructure): array
    {
        if (is_array($dataStructure)) {
            return $dataStructure;
        }
        // Taken from TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools
        if (strpos(trim($dataStructure), 'FILE:') === 0) {
            $file = GeneralUtility::getFileAbsFileName(substr(trim($dataStructure), 5));
            if (empty($file) || !@is_file($file)) {
                throw new RuntimeException(
                    'Data structure file ' . $file . ' could not be resolved to an existing file',
                    1478105826
                );
            }
            $dataStructure = (string) file_get_contents($file);
        }
        return GeneralUtility::xml2arrayProcess($dataStructure);
    }

    private static function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
