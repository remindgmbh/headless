<?php

// disabled for now; creates some weird type errors in the queryBuilder statements
//declare(strict_types=1);

namespace Remind\Headless\DataProcessing;

use FriendsOfTYPO3\Headless\DataProcessing\FilesProcessor;
use Generator;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Resource\FileCollector;

class AssociatedCategoryProcessor extends FilesProcessor
{
    // @todo for future: implement option to fetch categories missing parents to construct a category tree
    private $defaultTargets = [
        'filesProcessor' => 'files',
        'categoriesProcessor' => 'categoryFiles',
        'filesCategoriesRelations' => 'filesCategoriesRelations',
    ];

    private $uidList = [];

    /**
     * Process data for categories attached to records.
     * Uses processedData of previous Processors to fetch their attached categories, if available
     *
     * @param ContentObjectRenderer $cObj The content object renderer, which contains data of the content element
     * @param array $contentObjectConfiguration The configuration of Content Object
     * @param array $processorConfiguration The configuration of this processor
     * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
     * @return array the processed data as key/value store
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ) {
        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        $properties = [];

        if (isset($processorConfiguration['processingConfiguration.'])) {
            foreach (array_keys((array)$processorConfiguration['processingConfiguration.']) as $key) {
                $properties[$key] = $cObj->stdWrapValue($key, $processorConfiguration['processingConfiguration.'], null);
            }
        }

        $this->contentObjectRenderer = $cObj;
        $this->processorConfiguration = $processorConfiguration;


        // only get the first found value of array key 'as' in $contentObjectConfiguration,
        // as its bound to be the correct array key to access the processed files of a previous FilesProcessor
        $filesProcessorTargetArrayName = $this->recursiveFind($contentObjectConfiguration, 'as')->current();

        $categoryProcessorTargetArrayName = $processorConfiguration['as'] ?? $this->defaultTargets['categoriesProcessor'];
        $relationsTargetArrayName = $processorConfiguration['relationsAs'] ?? $this->defaultTargets['filesCategoriesRelations'];

        // data from files processor:
        $files = $processedData[$filesProcessorTargetArrayName] ?? $processedData[$this->defaultTargets['filesProcessor']] ?? [];

        $categoryInformation = [];

        foreach ($files as $file) {
            // compatibility with legacyReturn = 1 ( = file has ['properties'])
            // files coming from a file collection might have the value of their uidLocal set in ['fileReferenceUid']
            // with ['uidLocal'] being empty
            $uid = $file['uidLocal'] ?? $file['properties']['uidLocal'] ?? $file['fileReferenceUid'] ?? $file['properties']['fileReferenceUid'];
            // consideration: unset ['fileReferenceUid'] for files from file collection and set ['uidLocal'] = $uid
            // so the output is the same whether the file is coming from a file collection or elsewhere
            $catInfo = $this->fetchAssociatedCategoryInformation($uid);
            $categoryInformation[] =
            [
                'fileUid' => $uid,
                'categories' => $catInfo,
            ];
        }

        foreach ($this->recursiveFind($categoryInformation, 'frUid') as $uid) {
            if (!in_array($uid, $this->uidList)) {
                $this->uidList[] = $uid;
            }
        }

        $this->fileObjects = $this->fetchData();

        $processedData[$categoryProcessorTargetArrayName] = $this->processFiles($properties);

        $collectProcessedData = [];
        // insert array with $categoryProcessorTargetArrayName ( = $processorConfiguration['as'] ) as key to fill it with desired data,
        // otherwise it will be removed by subsequent methods of parent class
        $collectProcessedData[$categoryProcessorTargetArrayName] = [];
        $collectProcessedData[$categoryProcessorTargetArrayName][$filesProcessorTargetArrayName] = $files;
        $collectProcessedData[$categoryProcessorTargetArrayName][$categoryProcessorTargetArrayName] = $processedData[$categoryProcessorTargetArrayName];
        $collectProcessedData[$categoryProcessorTargetArrayName][$relationsTargetArrayName] = $categoryInformation;

        return $this->removeDataIfnotAppendInConfiguration($processorConfiguration, $collectProcessedData);
    }

    protected function fetchData(): array
    {
        $fileCollector = GeneralUtility::makeInstance(FileCollector::class);
        $fileCollector->addFileReferences($this->uidList);

        return $fileCollector->getFiles();
    }

    protected function fetchAssociatedCategoryInformation($uid): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_category');

        // despite what the 12.4 documentation about the query builder says,
        // AS in select statement cant handle a '.' in the identifier (turns out to be `fr`.`uid` instead of `fr.uid` and causes error)
        $qb = $queryBuilder
        ->select('fr.uid AS frUid', 'c.uid', 'c.title', 'c.parent')
        ->from('sys_category', 'c')
        ->join(
            'c',
            'sys_category_record_mm',
            'r',
            $queryBuilder->expr()->and(
                $queryBuilder->expr()->eq('r.uid_local', $queryBuilder->quoteIdentifier('c.uid')),
                $queryBuilder->expr()->eq('r.fieldname', $queryBuilder->createNamedParameter('categories')),
                $queryBuilder->expr()->eq('r.tablenames', $queryBuilder->createNamedParameter('sys_file_metadata')),
            )
        )
        ->join(
            'r',
            'sys_file_metadata',
            'm',
            $queryBuilder->expr()->eq('r.uid_foreign', $queryBuilder->quoteIdentifier('m.uid'))
        )
        ->join(
            'c',
            'sys_file_reference',
            'fr',
            $queryBuilder->expr()->and(
                $queryBuilder->expr()->eq('fr.uid_foreign', $queryBuilder->quoteIdentifier('c.uid')),
                $queryBuilder->expr()->eq('fr.fieldname', $queryBuilder->createNamedParameter('images')),
                $queryBuilder->expr()->eq('fr.tablenames', $queryBuilder->createNamedParameter('sys_category')),
            )
        )
        ->join(
            'fr',
            'sys_file',
            'f',
            $queryBuilder->expr()->eq('fr.uid_local', $queryBuilder->quoteIdentifier('f.uid')),
        )
        ->where(
            $queryBuilder->expr()->eq('m.file', $queryBuilder->createNamedParameter($uid)),
            $queryBuilder->expr()->eq('c.deleted', $queryBuilder->createNamedParameter(0)),
        );

        return $qb->executeQuery()->fetchAllAssociative();
    }

    protected function recursiveFind(array $haystack, string $needle): Generator
    {
        foreach ($haystack as $key => $value) {
            if (is_array($value)) {
                foreach ($this->recursiveFind($value, $needle) as $subArray) {
                    yield $subArray;
                }
            }
            if ($key === $needle) {
                yield $value;
            }
        }
    }
}
