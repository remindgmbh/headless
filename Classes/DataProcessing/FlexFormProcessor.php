<?php

declare(strict_types=1);

namespace Remind\Headless\DataProcessing;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;
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
        $flexFormTools = GeneralUtility::makeInstance(FlexFormTools::class);
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

        $fieldTca = $GLOBALS['TCA'][$table]['columns'][$fieldName];

        $dataStructureIdentifier = $flexFormTools->getDataStructureIdentifier(
            $fieldTca,
            $table,
            $fieldName,
            $processedData['data']
        );

        $dataStructure = $flexFormTools->parseDataStructureByIdentifier($dataStructureIdentifier);

        $flexFormData = $flexFormService->convertFlexFormContentToArray($processedData['data'][$fieldName]);

        $this->arrayWalkRecursiveWithKey(
            $flexFormData,
            function (&$value, $keys) use ($dataStructure, $cObj, $processorConf): void {
                $key = implode('.', $keys);
                $flexFormElement = $this->arraySearchByKeyRecursive($dataStructure, $key);
                $value = $this->parseFlexFormValue($flexFormElement, $value, $cObj, $processorConf);
            }
        );

        if (isset($processorConf['path'])) {
            $flexFormData = ArrayUtility::getValueByPath($flexFormData, $processorConf['path'], '.');
        }

        // section keys start at 1 instead of 0
        $flexFormData = $this->reNumberIndexes($flexFormData);

        // ignore fields determined in typoscript configuration
        $ignoredFields = GeneralUtility::trimExplode(',', $processorConf['ignoreFields'] ?? '', true);

        foreach ($ignoredFields as $ignoredField) {
            $flexFormData = ArrayUtility::removeByPath($flexFormData, $ignoredField, '.');
        }

        $overrideData = [];
        $overrideData = $this->contentDataProcessor?->process($cObj, $processorConf, $overrideData) ?? [];
        foreach ($overrideData as $key => &$value) {
            $flexFormData = empty($key) ? $value : ArrayUtility::setValueByPath($flexFormData, $key, $value, '.');
        }

        // save result in "data" (default) or given variable name
        $targetVariableName = $cObj->stdWrapValue('as', $processorConf);

        if (!empty($targetVariableName)) {
            $processedData[$targetVariableName] = $flexFormData;
        } else {
            $processedData['data'][$fieldName] = $flexFormData;
        }

        return $processedData;
    }

    /**
     * @param mixed[] $element
     * @param mixed[] $processorConf
     */
    private function parseFlexFormValue(
        ?array $element,
        string $value,
        ContentObjectRenderer $cObj,
        array $processorConf,
    ): mixed {
        $newValue = $value;

        $type = $element['config']['type'] ?? null;

        if ($type === 'link') {
            $newValue = $cObj->typoLink('', ['parameter' => $value, 'returnLast' => 'result']);
        }

        if ($type === 'check') {
            $newValue = (bool) $value;
        }

        if (($element['config']['eval'] ?? null) === 'int') {
            $newValue = (int) $value;
        }

        if ($type === 'text') {
            $newValue = $cObj->parseFunc($value, null, '< lib.parseFunc_links');
        }

        if ($type === 'file') {
            $fieldName = $element['config']['foreign_match_fields']['fieldname'] ?? null;

            if ($fieldName) {
                $overrule = [];

                try {
                    $overrule = ArrayUtility::getValueByPath(
                        $processorConf,
                        ['filesConfiguration.', ...array_map(function ($value) {
                            return $value . '.';
                        },
                        explode('.', $fieldName))]
                    );
                } catch (MissingArrayPathException $e) {
                }

                $filesService = GeneralUtility::makeInstance(FilesService::class);

                $newValue = $filesService->processImages(
                    $cObj->getCurrentTable(),
                    $fieldName,
                    $cObj->data['uid'],
                    $overrule
                );
            }
        }

        return $newValue;
    }

    /**
     * @param mixed[] $array
     * @param string[] $keys
     */
    private function arrayWalkRecursiveWithKey(array &$array, callable $callback, array $keys = []): void
    {
        foreach ($array as $key => &$value) {
            $keys[] = $key;
            if (is_array($value)) {
                $this->arrayWalkRecursiveWithKey($value, $callback, $keys);
            } else {
                call_user_func_array($callback, [&$value, $keys]);
            }
            array_pop($keys);
        }
    }

    /**
     * see: https://stackoverflow.com/a/3975706
     *
     * @param mixed[] $haystack
     */
    private function arraySearchByKeyRecursive(array $haystack, string $needle): mixed
    {
        $iterator = new RecursiveArrayIterator($haystack);
        $recursive = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($recursive as $key => $value) {
            if ($key === $needle) {
                return $value;
            }
        }
        return null;
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
