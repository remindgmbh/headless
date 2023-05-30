<?php

defined('TYPO3') || die;

use Remind\Headless\Preview\ContentWithItemsPreviewRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$hasBackground = [
    'AND' => [
        'FIELD:tx_headless_background_color:!=:none',
        'FIELD:tx_headless_background_color:REQ:true',
    ],
];

ExtensionManagementUtility::addTCAcolumns(
    'tt_content',
    [
        'tx_headless_background_color' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:background_color',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:background_color.none',
                        'value' => 'none',
                    ],
                ],
                'default' => 'none',
            ],
            'onChange' => 'reload',
        ],
        'tx_headless_background_full_width' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:background_full_width',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        'label' => '',
                        'value' => 0,
                    ],
                ],
            ],
            'displayCond' => $hasBackground,
        ],
        'tx_headless_cookie_category' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:cookie.category',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:cookie.category.none',
                        'value' => null,
                    ],
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:cookie.category.necessary',
                        'value' => 0,
                    ],
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:cookie.category.preferences',
                        'value' => 1,
                    ],
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:cookie.category.statistics',
                        'value' => 2,
                    ],
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:cookie.category.marketing',
                        'value' => 3,
                    ],
                ],
                'default' => null,
            ],
        ],
        'tx_headless_cookie_message' => [
            'l10n_mode' => 'prefixLangTitle',
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:cookie.message',
            'config' => [
                'type' => 'text',
                'cols' => 80,
                'rows' => 10,
                'softref' => 'typolink_tag,email[subst],url',
                'enableRichtext' => true,
            ],
        ],
        'tx_headless_item' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:tx_headless_item',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_headless_item',
                'foreign_field' => 'tt_content',
            ],
        ],
        'tx_headless_space_before_inside' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:space_before_inside',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
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
                'default' => '',
            ],
            'displayCond' => $hasBackground,
        ],
        'tx_headless_space_after_inside' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:space_after_inside',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
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
                'default' => '',
            ],
            'displayCond' => $hasBackground,
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

ExtensionManagementUtility::addToAllTCAtypes('tt_content', 'tx_headless_cookie_category');
ExtensionManagementUtility::addToAllTCAtypes('tt_content', 'tx_headless_cookie_message');

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
    $GLOBALS
        ['TCA']
        ['tt_content']
        ['columns']
        ['space_after_class']
        ['config']
        ['items']
        [0]
        ['label'] = 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:space_none';
    $GLOBALS
        ['TCA']
        ['tt_content']
        ['columns']
        ['space_before_class']
        ['config']
        ['items']
        [0]
        ['label'] = 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:space_none';
