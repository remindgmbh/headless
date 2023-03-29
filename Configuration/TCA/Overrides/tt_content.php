<?php

defined('TYPO3_MODE') || die;

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
        'header_layout' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:header_layout.text', '0'],
                    ['H1', '1'],
                    ['H2', '2'],
                    ['H3', '3'],
                    ['H4', '4'],
                    ['H5', '5'],
                    ['H6', '6'],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_layout.I.6', '100'],
                ],
                'default' => 0,
            ],
        ],
        'tx_headless_background_color' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:background_color',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:background_color.none', 'none'],
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:background_color.primary', 'primary'],
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:background_color.secondary', 'secondary'],
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:background_color.accent', 'accent'],
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:background_color.white', 'white'],
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:background_color.black', 'black'],
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
                        0 => '',
                        1 => '',
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
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:cookie.category.none', null],
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:cookie.category.necessary', 0],
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:cookie.category.preferences', 1],
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:cookie.category.statistics', 2],
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:cookie.category.marketing', 3],
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
                'softref' => 'typolink_tag,images,email[subst],url',
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
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:space_none', ''],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_extra_small', 'extra-small'],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_small', 'small'],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_medium', 'medium'],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_large', 'large'],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_extra_large', 'extra-large'],
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
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:space_none', ''],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_extra_small', 'extra-small'],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_small', 'small'],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_medium', 'medium'],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_large', 'large'],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_extra_large', 'extra-large'],
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
