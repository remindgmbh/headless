<?php

declare(strict_types=1);

namespace Remind\Headless\DataProcessing;

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

        // remove unnecessary nesting in section structure
        $flexformData = array_map(function ($value) {
            // check if $value is an array and contains only numeric keys
            if (is_array($value) && empty(array_filter(array_keys($value), 'is_string'))) {
                return array_reduce($value, function (array $result, mixed $value) {
                    $result[] = is_array($value) && count($value) === 1 ? array_pop($value) : $value;
                    return $result;
                }, []);
            }
            return $value;
        }, $flexformData);

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

        if (($element['config']['type'] ?? null) === 'link') {
            $newValue = $this->cObj->typoLink('', ['parameter' => $value, 'returnLast' => 'result']);
        }

        if (($element['config']['type'] ?? null) === 'check') {
            $newValue = (bool) $value;
        }

        if (($element['config']['eval'] ?? null) === 'int') {
            $newValue = (int) $value;
        }

        if (($element['config']['type'] ?? null) === 'text') {
            $newValue = $this->cObj->parseFunc($value, [], '< lib.parseFunc_links');
        }

        $flexFormTools->cleanFlexFormXML = ArrayUtility::setValueByPath(
            $flexFormTools->cleanFlexFormXML,
            $path,
            $newValue,
        );
    }
}
