<?php

declare(strict_types=1);

namespace Remind\Headless\DataProcessing;

use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class FlexFormProcessor implements DataProcessorInterface
{
    protected ContentObjectRenderer $cObj;

    protected FlexFormService $flexFormService;

    protected FlexFormTools $flexFormTools;
    
    public function __construct()
    {
        $this->flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $this->flexFormTools = GeneralUtility::makeInstance(FlexFormTools::class);
    }

    /**
     * @param ContentObjectRenderer $cObj The data of the content element or page
     * @param array $contentObjectConfiguration The configuration of Content Object
     * @param array $processorConfiguration The configuration of this processor
     * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
     * @return array the processed data as key/value store
     */
    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData): array
    {
        $this->cObj = $cObj;
        $fieldName = $cObj->stdWrapValue('fieldName', $processorConfiguration);

        // default flexform field name
        if (empty($fieldName)) {
            $fieldName = 'pi_flexform';
        }

        if (!$processedData['data'][$fieldName]) {
            return $processedData;
        }

        $table = $cObj->getCurrentTable();

        $this->flexFormTools->cleanFlexFormXML($table, $fieldName, $processedData['data']);

        $this->flexFormTools->traverseFlexFormXMLData($table, $fieldName, $processedData['data'], $this, 'convertUrl');

        $dataWithConvertedUrls = $this->flexFormTools->cleanFlexFormXML;

        $xmlString = $this->flexFormTools->flexArray2Xml($dataWithConvertedUrls, true);

        $flexformData = $this->flexFormService->convertFlexFormContentToArray($xmlString);

        // save result in "data" (default) or given variable name
        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration);

        if (!empty($targetVariableName)) {
            $processedData[$targetVariableName] = $flexformData;
        } else {
            $processedData['data'][$fieldName] = $flexformData;
        }

        return $processedData;
    }

    function convertUrl(array $element, string $value, $PA, string $path, FlexFormTools $flexFormTools): void
    {
        if ($element['TCEforms']['config']['renderType'] === 'inputLink') {
            $flexFormTools->setArrayValueByPath($path, $flexFormTools->cleanFlexFormXML, $this->cObj->getTypoLink_URL($value));
        }
    }
}
