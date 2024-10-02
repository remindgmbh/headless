<?php

declare(strict_types=1);

namespace Remind\Headless\DataProcessing;

use Remind\Headless\Service\FilesService;
use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\Exception\MissingArrayPathException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentDataProcessor;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class FlexFormProcessor implements DataProcessorInterface
{
    protected ContentObjectRenderer $cObj;

    /**
     * @var mixed[]
     */
    protected array $processorConf;

    protected ?ContentDataProcessor $contentDataProcessor = null;

    public function __construct()
    {
        $this->contentDataProcessor = GeneralUtility::makeInstance(ContentDataProcessor::class);
    }

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
        $this->cObj = $cObj;
        $this->processorConf = $processorConf;

        $flexFormTools = GeneralUtility::makeInstance(FlexFormTools::class);
        $flexFormTools->reNumberIndexesOfSectionData = true;
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);

        $fieldName = (string) $cObj->stdWrapValue('fieldName', $processorConf);

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
                [
                    ...$flexFormTools->flexArray2Xml_options,
                    'disableTypeAttrib' => 0,
                ]
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

        $overrideData = [];
        $overrideData = $this->contentDataProcessor?->process($cObj, $processorConf, $overrideData) ?? [];
        foreach ($overrideData as $key => &$value) {
            $flexformData = empty($key) ? $value : ArrayUtility::setValueByPath($flexformData, $key, $value, '.');
        }

        // save result in "data" (default) or given variable name
        $targetVariableName = $cObj->stdWrapValue('as', $processorConf);

        if (!empty($targetVariableName)) {
            $processedData[$targetVariableName] = $flexformData;
        } else {
            $processedData['data'][$fieldName] = $flexformData;
        }

        return $processedData;
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     * @param mixed[] $element
     */
    public function parseElement(
        array $element,
        string $value,
        mixed $additionalParameters,
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
            $newValue = $this->cObj->parseFunc($value, null, '< lib.parseFunc_links');
        }

        if ($type === 'file') {
            $fieldName = $element['config']['foreign_match_fields']['fieldname'];

            $overrule = [];

            try {
                $overrule = ArrayUtility::getValueByPath(
                    $this->processorConf,
                    ['filesConfiguration.', ...array_map(function ($value) {
                        return $value . '.';
                    },
                    explode('.', $fieldName))]
                );
            } catch (MissingArrayPathException $e) {
            }

            $filesService = GeneralUtility::makeInstance(FilesService::class);

            $newValue = $filesService->processImages(
                $this->cObj->getCurrentTable(),
                $fieldName,
                $this->cObj->data['uid'],
                $overrule
            );
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
