<?php

declare(strict_types=1);

namespace Remind\Headless\DataProcessing;

use FriendsOfTYPO3\Headless\DataProcessing\FilesProcessor;
use Remind\Headless\Service\FilesService;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class FilesCategoryProcessor implements DataProcessorInterface
{
    private const SYS_CATEGORY = 'sys_category';
    private const SYS_CATEGORY_RECORD_MM = 'sys_category_record_mm';
    private const SYS_FILE_METADATA = 'sys_file_metadata';

    /** @var mixed[] $categories */
    private array $categories = [];

    /** @var mixed[] $processorConf */
    private array $processorConf = [];

    private bool $legacyReturn = true;

    private ConnectionPool $connectionPool;

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     * @param mixed[] $contentObjectConf
     * @param mixed[] $processorConf
     * @param mixed[] $processedData
     * @return mixed[]
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConf,
        array $processorConf,
        array $processedData
    ): array {
        $this->processorConf = $processorConf;
        $this->connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

        $filesProcessorIndex = array_search(FilesProcessor::class, $contentObjectConf['dataProcessing.']);
        if ($filesProcessorIndex !== false) {
            $filesProcessorConf = $contentObjectConf['dataProcessing.'][$filesProcessorIndex . '.'];
            $filesAs = $filesProcessorConf['as'];

            if (isset($processedData[$filesAs])) {
                $categoriesAs = $processorConf['as'] ?? 'categories';
                $this->legacyReturn = (bool) ($filesProcessorConf['processingConfiguration.']['legacyReturn'] ?? true);
                $fileUids = [];
                foreach ($processedData[$filesAs] as &$file) {
                    $uid = $this->getFileUid($file);
                    if ($uid) {
                        $fileUids[] = (int) $uid;
                    }
                }
                $fileCategoryMap = $this->getFileCategoryMap($fileUids);

                $this->categories = array_fill_keys(array_unique(array_merge(...$fileCategoryMap)), []);

                $this->fetchCategoryProperties();

                if (!($processorConf['images.']['disabled'] ?? false)) {
                    $this->fetchCategoryImages();
                }

                foreach ($processedData[$filesAs] as &$file) {
                    $uid = $this->getFileUid($file);
                    $file = ArrayUtility::setValueByPath(
                        $file,
                        $this->legacyReturn ? ['properties', $categoriesAs] : $categoriesAs,
                        array_map(function (int $categoryUid) {
                            return $this->categories[$categoryUid];
                        }, $fileCategoryMap[$uid] ?? [])
                    );
                }
            }
        }
        return $processedData;
    }

    /**
     * @param mixed[] $file
     */
    private function getFileUid(array $file): int
    {
        // uidLocal may be null while fileReferenceUid contains the actual file uid
        // see: https://github.com/TYPO3-Headless/headless/pull/761
        return $this->legacyReturn
            ? $file['properties']['uidLocal'] ?? $file['properties']['fileReferenceUid']
            : $file['uidLocal'] ?? $file['fileReferenceUid'];
    }

    /**
     * @param int[] $fileUids
     * @return mixed[]
     */
    private function getFileCategoryMap(array $fileUids): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::SYS_CATEGORY);

        $queryBuilder = $queryBuilder
            ->select(
                self::SYS_CATEGORY_RECORD_MM . '.uid_local',
                self::SYS_FILE_METADATA . '.file',
            )
            ->distinct()
            ->from(self::SYS_CATEGORY_RECORD_MM)
            ->join(
                self::SYS_CATEGORY_RECORD_MM,
                self::SYS_FILE_METADATA,
                self::SYS_FILE_METADATA,
                (string) $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq(
                        self::SYS_CATEGORY_RECORD_MM . '.uid_foreign',
                        $queryBuilder->quoteIdentifier(self::SYS_FILE_METADATA . '.uid')
                    ),
                    $queryBuilder->expr()->eq(
                        self::SYS_CATEGORY_RECORD_MM . '.fieldname',
                        $queryBuilder->createNamedParameter('categories')
                    ),
                    $queryBuilder->expr()->eq(
                        self::SYS_CATEGORY_RECORD_MM . '.tablenames',
                        $queryBuilder->createNamedParameter(self::SYS_FILE_METADATA)
                    ),
                )
            )
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->in(
                        self::SYS_FILE_METADATA . '.file',
                        $queryBuilder->createNamedParameter($fileUids, Connection::PARAM_INT_ARRAY),
                    )
                )
            );

        $queryResult = $queryBuilder->executeQuery();

        $result = [];

        while ($row = $queryResult->fetchAssociative()) {
            if (!isset($result[$row['file']])) {
                $result[$row['file']] = [];
            }
            $result[$row['file']][] = $row['uid_local'];
        }

        return $result;
    }

    private function fetchCategoryProperties(): void
    {
        $fields = isset($this->processorConf['fields'])
            ? GeneralUtility::trimExplode(',', $this->processorConf['fields'])
            : ['title'];

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::SYS_CATEGORY);

        $queryBuilder = $queryBuilder
            ->select(
                'uid',
                ...$fields,
            )
            ->distinct()
            ->from(self::SYS_CATEGORY)
            ->where(
                $queryBuilder->expr()->in(
                    'uid',
                    $queryBuilder->createNamedParameter(array_keys($this->categories), Connection::PARAM_INT_ARRAY),
                )
            );

            $queryResult = $queryBuilder->executeQuery();


        while ($row = $queryResult->fetchAssociative()) {
            $this->categories[$row['uid']] = $row;
        }
    }

    private function fetchCategoryImages(): void
    {
        $filesService = GeneralUtility::makeInstance(FilesService::class);
        $imageField = $this->processorConf['images.']['field'] ?? 'image';

        $processingConfiguration = $this->processorConf['images.']['processingConfiguration.'] ?? [];

        foreach (array_keys($this->categories) as $categoryUid) {
            $this->categories[$categoryUid][$imageField] = $filesService->processImages(
                self::SYS_CATEGORY,
                'images',
                $categoryUid,
                $processingConfiguration,
            );
        }
    }
}
