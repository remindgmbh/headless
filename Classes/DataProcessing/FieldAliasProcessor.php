<?php

declare(strict_types=1);

namespace Remind\Headless\DataProcessing;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Copies a record field to a differently named key in the processed data, e.g. to
 * expose "tx_headless_overview_label" as "overviewLabel" on menu items.
 */
class FieldAliasProcessor implements DataProcessorInterface
{
    /**
     * @param mixed[] $contentObjectConfiguration
     * @param mixed[] $processorConfiguration
     * @param mixed[] $processedData
     * @return mixed[]
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $fieldName = (string) $cObj->stdWrapValue('field', $processorConfiguration);
        if ($fieldName === '') {
            return $processedData;
        }

        $as = (string) $cObj->stdWrapValue('as', $processorConfiguration, $fieldName);
        $value = (string) ($cObj->data[$fieldName] ?? '');
        $processedData[$as] = $cObj->stdWrap($value, $processorConfiguration['stdWrap.'] ?? []);

        return $processedData;
    }
}
