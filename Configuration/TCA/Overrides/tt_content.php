<?php

declare(strict_types=1);

defined('TYPO3') || die;

use Remind\Headless\Preview\ContentWithItemsPreviewRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addTCAcolumns(
    'tt_content',
    [
        'tx_headless_background_color' => [
            'config' => [
                'default' => null,
                'items' => [
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:background_color.none',
                        'value' => null,
                    ],
                ],
                'renderType' => 'selectSingle',
                'type' => 'select',
            ],
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:background_color',
            'onChange' => 'reload',
        ],
        'tx_headless_background_full_width' => [
            'config' => [
                'items' => [
                    [
                        'label' => '',
                        'value' => 0,
                    ],
                ],
                'renderType' => 'checkboxToggle',
                'type' => 'check',
            ],
            'displayCond' => 'FIELD:tx_headless_background_color:REQ:true',
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:background_full_width',
        ],
        'tx_headless_cookie_category' => [
            'config' => [
                'default' => null,
                'items' => [
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:cookies.category.none',
                        'value' => null,
                    ],
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:cookies.category.necessary',
                        'value' => 0,
                    ],
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:cookies.category.preferences',
                        'value' => 1,
                    ],
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:cookies.category.statistics',
                        'value' => 2,
                    ],
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:cookies.category.marketing',
                        'value' => 3,
                    ],
                ],
                'renderType' => 'selectSingle',
                'type' => 'select',
            ],
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:cookies.category',
        ],
        'tx_headless_cookie_message' => [
            'config' => [
                'cols' => 80,
                'enableRichtext' => true,
                'rows' => 10,
                'softref' => 'typolink_tag,email[subst],url',
                'type' => 'text',
            ],
            'l10n_mode' => 'prefixLangTitle',
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:cookies.message',
        ],
        'tx_headless_item' => [
            'config' => [
                'foreign_field' => 'foreign_uid',
                'foreign_table' => 'tx_headless_item',
                'foreign_table_field' => 'foreign_table',
                'type' => 'inline',
            ],
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:tx_headless_item',
        ],
        'tx_headless_space_after_inside' => [
            'config' => [
                'default' => '',
                'items' => [
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:space_none',
                        'value' => '',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_extra_small',
                        'value' => 'extra-small',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_small',
                        'value' => 'small',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_medium',
                        'value' => 'medium',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_large',
                        'value' => 'large',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_extra_large',
                        'value' => 'extra-large',
                    ],
                ],
                'renderType' => 'selectSingle',
                'type' => 'select',
            ],
            'displayCond' => 'FIELD:tx_headless_background_color:REQ:true',
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:space_after_inside',
        ],
        'tx_headless_space_before_inside' => [
            'config' => [
                'default' => '',
                'items' => [
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:space_none',
                        'value' => '',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_extra_small',
                        'value' => 'extra-small',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_small',
                        'value' => 'small',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_medium',
                        'value' => 'medium',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_large',
                        'value' => 'large',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_extra_large',
                        'value' => 'extra-large',
                    ],
                ],
                'renderType' => 'selectSingle',
                'type' => 'select',
            ],
            'displayCond' => 'FIELD:tx_headless_background_color:REQ:true',
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:space_before_inside',
        ],
    ]
);

ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'frames',
    '--linebreak--,tx_headless_background_color,tx_headless_background_full_width,--linebreak--',
    'after:frame_class'
);

ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'frames',
    'tx_headless_space_before_inside',
    'after:space_before_class'
);

ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'frames',
    'tx_headless_space_after_inside',
    'after:space_after_class'
);

/**
 * Palette will be added in AfterTcaCompilationEventListener so Content Elements
 * added in Extensions after this one will also have the palette
 */
ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'cookies',
    'tx_headless_cookie_category,--linebreak--,tx_headless_cookie_message',
);

$GLOBALS['TCA']['tt_content']['ctrl']['previewRenderer'] = ContentWithItemsPreviewRenderer::class;

$GLOBALS['TCA']['tt_content']['columns']['header_layout']['config']['items'] = [
    [
        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:header_layout.text',
        'value' => '0',
    ],
    [
        'label' => 'H1',
        'value' => '1',
    ],
    [
        'label' => 'H2',
        'value' => '2',
    ],
    [
        'label' => 'H3',
        'value' => '3',
    ],
    [
        'label' => 'H4',
        'value' => '4',
    ],
    [
        'label' => 'H5',
        'value' => '5',
    ],
    [
        'label' => 'H6',
        'value' => '6',
    ],
    [
        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_layout.I.6',
        'value' => '100',
    ],
];

// Workaround for TCEFORM (https://forge.typo3.org/issues/100775)
$GLOBALS['TCA']['tt_content']['columns']['space_after_class']['config']['items'][0]['label'] = 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:space_none';
$GLOBALS['TCA']['tt_content']['columns']['space_before_class']['config']['items'][0]['label'] = 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:space_none';
