<?php

declare(strict_types=1);

namespace Remind\Typo3Headless\DataProcessing;

use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Hooks\DataStructureIdentifierHook;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class FlexFormProcessor implements DataProcessorInterface
{
    protected ContentObjectRenderer $cObj;

    protected array $processorConf;

    protected FlexFormService $flexFormService;

    protected FlexFormTools $flexFormTools;

    public function __construct()
    {
        $this->flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $this->flexFormTools = GeneralUtility::makeInstance(FlexFormTools::class);
    }

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
        $this->processorConf = $processorConf;
        $fieldName = $cObj->stdWrapValue('fieldName', $processorConf);

        // default flexform field name
        if (empty($fieldName)) {
            $fieldName = 'pi_flexform';
        }

        if (!$processedData['data'][$fieldName]) {
            return $processedData;
        }

        $table = $cObj->getCurrentTable();

        // Workaround for https://forge.typo3.org/issues/97972 since local patches from packages don't work
        // (see https://github.com/cweagans/composer-patches/issues/339)
        $hook = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][FlexFormTools::class]['flexParsing'][DataStructureIdentifierHook::class];

        if ($hook) {
            unset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][FlexFormTools::class]['flexParsing'][DataStructureIdentifierHook::class]);
        }

        $this->flexFormTools->cleanFlexFormXML($table, $fieldName, $processedData['data']);

        $this->flexFormTools->traverseFlexFormXMLData(
            $table,
            $fieldName,
            $processedData['data'],
            $this,
            'parseElement'
        );

        if ($hook) {
            $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][FlexFormTools::class]['flexParsing'][DataStructureIdentifierHook::class] = $hook;
        }

        $flexformData = $this->convertFlexFormContentToArray($this->flexFormTools->cleanFlexFormXML);

        // save result in "data" (default) or given variable name
        $targetVariableName = $cObj->stdWrapValue('as', $processorConf);

        if (!empty($targetVariableName)) {
            $processedData[$targetVariableName] = $flexformData;
        } else {
            $processedData['data'][$fieldName] = $flexformData;
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
        if (($element['TCEforms']['config']['renderType'] ?? null) === 'inputLink') {
            $link = $this->cObj->getTypoLink_URL($value);
            $flexFormTools->cleanFlexFormXML = ArrayUtility::setValueByPath($flexFormTools->cleanFlexFormXML, $path, $link);
            return;
        }

        if (($element['TCEforms']['config']['type'] ?? null) === 'check') {
            $flexFormTools->cleanFlexFormXML = ArrayUtility::setValueByPath($flexFormTools->cleanFlexFormXML, $path, (bool)$value);
            return;
        }

        if (($element['TCEforms']['config']['eval'] ?? null) === 'int') {
            $flexFormTools->cleanFlexFormXML = ArrayUtility::setValueByPath($flexFormTools->cleanFlexFormXML, $path, (int)$value);
            return;
        }

        if (($element['TCEforms']['config']['type'] ?? null) === 'text' && $this->processorConf['parseFunc']) {
            $content = $this->cObj->parseFunc($value, [], $this->processorConf['parseFunc']);
            $flexFormTools->cleanFlexFormXML = ArrayUtility::setValueByPath($flexFormTools->cleanFlexFormXML, $path, $content);
            return;
        }
    }

    // taken from TYPO3\CMS\Core\Service\FlexFormService but without converting from string to array first
    protected function convertFlexFormContentToArray(
        $flexFormArray,
        $languagePointer = 'lDEF',
        $valuePointer = 'vDEF'
    ): array {
        $settings = [];
        $flexFormArray = $flexFormArray['data'] ?? [];
        foreach (array_values($flexFormArray) as $languages) {
            if (!is_array($languages[$languagePointer])) {
                continue;
            }
            foreach ($languages[$languagePointer] as $valueKey => $valueDefinition) {
                if (strpos($valueKey, '.') === false) {
                    $settings[$valueKey] = $this->flexFormService->walkFlexFormNode($valueDefinition, $valuePointer);
                } else {
                    $valueKeyParts = explode('.', $valueKey);
                    $currentNode = &$settings;
                    foreach ($valueKeyParts as $valueKeyPart) {
                        $currentNode = &$currentNode[$valueKeyPart];
                    }
                    if (is_array($valueDefinition)) {
                        if (array_key_exists($valuePointer, $valueDefinition)) {
                            $currentNode = $valueDefinition[$valuePointer];
                        } else {
                            $currentNode = $this->flexFormService->walkFlexFormNode($valueDefinition, $valuePointer);
                        }
                    } else {
                        $currentNode = $valueDefinition;
                    }
                }
            }
        }
        return $settings;
    }
}
