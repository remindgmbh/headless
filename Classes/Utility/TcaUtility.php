<?php

declare(strict_types=1);

namespace Remind\Headless\Utility;

use RuntimeException;
use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TcaUtility
{
    /**
     * @param mixed[] $variants array with breakpoint names as key and aspect-ratios as value
     * the value can contain multple aspect ratios consisting of value and title
     * e.g. [
     *          'lg' => [
     *              ['value' => 8 / 5, 'title' => '8:5 (default)'],
     *              ['value' => 2 / 1, 'title' => '2:1 (alternative layout)'],
     *          ],
     *          'xxl' => [
     *              ['value' => 16 / 9, 'title' => '16:9']
     *          ]
     *      ]
     * @return mixed[]
     */
    public static function getCropVariants(array $variants): array
    {
        return array_reduce(array_keys($variants), function (array $result, string $breakpoint) use ($variants) {
            $aspectRatios = $variants[$breakpoint];
            $result[$breakpoint] = [
                'allowedAspectRatios' => array_reduce(array_keys($aspectRatios), function (
                    array $result,
                    string|int $key
                ) use (
                    $aspectRatios,
                    $breakpoint,
                ) {
                    $aspectRatio = $aspectRatios[$key];
                    $result[$breakpoint . '_' . $aspectRatio['value']] = $aspectRatio;
                    return $result;
                }, []),
                'title' => $breakpoint,
            ];
            return $result;
        }, []);
    }

    /**
     * @param mixed[] $breakpoints
     * @return mixed[]
     */
    public static function getCropVariantsFree(array $breakpoints): array
    {
        return self::getCropVariants(
            array_reduce(
                $breakpoints,
                function (array $result, string $breakpoint) {
                    $result[$breakpoint] = [
                        [
                            'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.free',
                            'value' => 0.0,
                        ],
                    ];
                    return $result;
                },
                []
            )
        );
    }

    /**
     * @param mixed[]|string $dataStructure either a xml flexform file path, a xml flexform string or a flexform array
     */
    public static function addPageConfigFlexForm(array|string $dataStructure): void
    {
        $newFlexFormArray = self::getFlexFormArray($dataStructure);
        $currentFlexFormArray = self::getFlexFormArray(
            $GLOBALS['TCA']['pages']['columns']['tx_headless_config']['config']['ds']['default']
        );

        if (($currentFlexFormArray['ROOT']['el'] ?? null) === '') {
            $currentFlexFormArray = [];
        }

        ArrayUtility::mergeRecursiveWithOverrule($currentFlexFormArray, $newFlexFormArray);
        $flexFormTools = GeneralUtility::makeInstance(FlexFormTools::class);
        $newFlexFormString = $flexFormTools->flexArray2Xml($currentFlexFormArray, true);

        $GLOBALS['TCA']['pages']['columns']['tx_headless_config']['config']['ds']['default'] = $newFlexFormString;
    }

    /**
     * @param mixed[]|string $dataStructure either a xml flexform file path, a xml flexform string or a flexform array
     */
    public static function setFooterFlexForm(array|string $dataStructure): void
    {
        $newFlexFormArray = self::getFlexFormArray($dataStructure);
        $flexFormTools = GeneralUtility::makeInstance(FlexFormTools::class);
        $newFlexFormString = $flexFormTools->flexArray2Xml($newFlexFormArray, true);

        $GLOBALS['TCA']['pages']['columns']['tx_headless_footer']['config']['ds']['default'] = $newFlexFormString;
    }

    /**
     * @param mixed[]|string $dataStructure either a xml flexform file path, a xml flexform string or a flexform array
     * @return mixed[]
     */
    private static function getFlexFormArray(array|string $dataStructure): array
    {
        if (is_array($dataStructure)) {
            return $dataStructure;
        }
        // Taken from TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools
        if (strpos(trim($dataStructure), 'FILE:') === 0) {
            $file = GeneralUtility::getFileAbsFileName(substr(trim($dataStructure), 5));
            if (
                empty($file) ||
                !@is_file($file)
            ) {
                throw new RuntimeException(
                    'Data structure file ' . $file . ' could not be resolved to an existing file',
                    1478105826
                );
            }
            $dataStructure = (string) file_get_contents($file);
        }
        return GeneralUtility::xml2arrayProcess($dataStructure);
    }
}
