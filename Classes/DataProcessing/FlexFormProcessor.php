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
            GeneralUtility::array2xml($flexFormTools->cleanFlexFormXML)
        );

        // remove unnecessary nesting in section structure
        $flexformData = array_map(function ($value) {
            if (is_array($value)) {
                return array_reduce($value, function (array $result, array $value) {
                    $result[] = array_pop($value);
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

        if (($element['TCEforms']['config']['renderType'] ?? null) === 'inputLink') {
            $newValue = $this->cObj->typoLink('', ['parameter' => $value, 'returnLast' => 'result']);
        }

        if (($element['TCEforms']['config']['type'] ?? null) === 'check') {
            $newValue = (bool) $value;
        }

        if (($element['TCEforms']['config']['eval'] ?? null) === 'int') {
            $newValue = (int) $value;
        }

        if (($element['TCEforms']['config']['type'] ?? null) === 'text') {
            $newValue = $this->cObj->parseFunc($value, [], '< lib.parseFunc_links');
        }

        $flexFormTools->cleanFlexFormXML = ArrayUtility::setValueByPath(
            $flexFormTools->cleanFlexFormXML,
            $path,
            $newValue,
        );
    }
}
