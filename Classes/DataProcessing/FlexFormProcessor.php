<?php

declare(strict_types=1);

namespace Remind\Headless\DataProcessing;

use FriendsOfTYPO3\Headless\DataProcessing\FilesProcessor;
use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class FlexFormProcessor implements DataProcessorInterface
{
    protected ContentObjectRenderer $cObj;

    /**
     * @param ContentObjectRenderer $cObj The data of the content element or page
     * @param array $contentObjectConf The configuration of Content Object
     * @param array $processorConf The configuration of this processor
     * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
     * @return array the processed data as key/value store
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConf,
        array $processorConf,
        array $processedData
    ): array {
        $this->cObj = $cObj;

        $flexFormTools = GeneralUtility::makeInstance(FlexFormTools::class);
        $flexFormTools->reNumberIndexesOfSectionData = true;
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);

        $fieldName = $cObj->stdWrapValue('fieldName', $processorConf);

        // default flexform field name
        if (empty($fieldName)) {
            $fieldName = 'pi_flexform';
        }

        if (!$processedData['data'][$fieldName]) {
            return $processedData;
        }

        $table = $cObj->getCurrentTable();

        $flexFormTools->cleanFlexFormXML($table, $fieldName, $processedData['data']);

        $flexFormTools->traverseFlexFormXMLData(
            $table,
            $fieldName,
            $processedData['data'],
            $this,
            'parseElement'
        );

        $flexformData = $flexFormService->convertFlexFormContentToArray(
            GeneralUtility::array2xml(
                $flexFormTools->cleanFlexFormXML,
                '',
                0,
                'T3FlexForms',
                0,
                $flexFormTools->flexArray2Xml_options
            )
        );

        if (isset($processorConf['path'])) {
            $flexformData = ArrayUtility::getValueByPath($flexformData, $processorConf['path'], '.');
        }

        // section keys start at 1 instead of 0
        $flexformData = $this->reNumberIndexes($flexformData);

        // ignore fields determined in typoscript configuration
        $ignoredFields = GeneralUtility::trimExplode(',', $processorConf['ignoreFields'] ?? '', true);

        foreach ($ignoredFields as $ignoredField) {
            $flexformData = ArrayUtility::removeByPath($flexformData, $ignoredField, '.');
        }

        // save result in "data" (default) or given variable name
        $targetVariableName = $cObj->stdWrapValue('as', $processorConf);

        if (!empty($targetVariableName)) {
            $processedData[$targetVariableName] = $flexformData;
        } else {
            if ($processedData['data'][$fieldName]) {
                $processedData['data'][$fieldName] = $flexformData;
            } else {
                $processedData[$fieldName] = $flexformData;
            }
        }

        return $processedData;
    }

    public function parseElement(
        array $element,
        string $value,
        $additionalParameters,
        string $path,
        FlexFormTools $flexFormTools
    ): void {
        $newValue = $value;

        $type = $element['config']['type'] ?? null;

        if ($type === 'link') {
            $newValue = $this->cObj->typoLink('', ['parameter' => $value, 'returnLast' => 'result']);
        }

        if ($type === 'check') {
            $newValue = (bool) $value;
        }

        if (($element['config']['eval'] ?? null) === 'int') {
            $newValue = (int) $value;
        }

        if ($type === 'text') {
            $newValue = $this->cObj->parseFunc($value, [], '< lib.parseFunc_links');
        }

        if ($type === 'file') {
            $fieldName = $element['config']['foreign_match_fields']['fieldname'];
            $filesProcessor = GeneralUtility::makeInstance(FilesProcessor::class);
            $as = 'file';
            $processorConfiguration = [
                'as' => $as,
                'references.' => [
                    'fieldName' => $fieldName,
                ],
            ];
            $processedData = [
                'data' => $this->cObj->data,
                'current' => null,
            ];
            $processedData = $filesProcessor->process(
                $this->cObj,
                [
                    'dataProcessing.' => [
                        '10' => FilesProcessor::class,
                        '10.' => $processorConfiguration,
                    ],
                ],
                $processorConfiguration,
                $processedData,
            );
            $newValue = $processedData[$as];
        }

        $flexFormTools->cleanFlexFormXML = ArrayUtility::setValueByPath(
            $flexFormTools->cleanFlexFormXML,
            $path,
            $newValue,
        );
    }

    private function reNumberIndexes(mixed $value): mixed
    {
        if (is_array($value)) {
            if (empty(array_filter(array_keys($value), 'is_string'))) {
                $value = array_values($value);
            }
            $value = array_map(function ($value) {
                return $this->reNumberIndexes($value);
            }, $value);
        }
        return $value;
    }
}
