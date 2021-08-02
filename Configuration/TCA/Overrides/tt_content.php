<?php
defined('TYPO3_MODE') || die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'tt_content',
    [
        'header_layout' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/Backend.xlf:tt_content.header_layout.text','0'],
                    ['H1','1'],
                    ['H2','2'],
                    ['H3','3'],
                    ['H4','4'],
                    ['H5','5'],
                    ['H6','6'],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_layout.I.6','100']
                ],
                'default' => 0
            ]
        ],
        'background_color' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/Backend.xlf:tt_content.background_color',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/Backend.xlf:tt_content.background_color.none', ''],
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/Backend.xlf:tt_content.background_color.primary', 'primary'],
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/Backend.xlf:tt_content.background_color.secondary', 'secondary'],
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/Backend.xlf:tt_content.background_color.accent', 'accent'],
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/Backend.xlf:tt_content.background_color.white', 'white'],
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/Backend.xlf:tt_content.background_color.black', 'black'],
                ],
                'default' => ''
            ],
            'onChange' => 'reload',
            'displayCond' => 'FIELD:colPos:!=:-1',
        ],
        'background_full_width' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/Backend.xlf:tt_content.background_full_width',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                   [
                      0 => '',
                      1 => '',
                   ]
                ],
            ],
            'displayCond' => 'FIELD:background_color:REQ:true',
        ],
        'space_before_inside' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/Backend.xlf:tt_content.space_before_inside',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.default_value', ''],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_extra_small', 'extra-small'],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_small', 'small'],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_medium', 'medium'],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_large', 'large'],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_extra_large', 'extra-large'],
                ],
                'default' => ''
            ],
            'displayCond' => 'FIELD:background_color:REQ:true',
        ],
        'space_after_inside' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/Backend.xlf:tt_content.space_after_inside',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.default_value', ''],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_extra_small', 'extra-small'],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_small', 'small'],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_medium', 'medium'],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_large', 'large'],
                    ['LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_extra_large', 'extra-large'],
                ],
                'default' => ''
            ],
            'displayCond' => 'FIELD:background_color:REQ:true',
        ],
        'tx_gridelements_title' => [
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/Backend.xlf:tt_content.tx_gridelements_title',
            'config' => [
                'type' => 'input',
            ],
            'displayCond' => 'FIELD:colPos:=:-1',
        ],
    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'frames',
    '--linebreak--,background_color',
    'after:frame_class'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'frames',
    'background_full_width,--linebreak--',
    'after:background_color'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'frames',
    'space_before_inside',
    'after:space_before_class'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'frames',
    'space_after_inside',
    'after:space_after_class'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'headers',
    '--linebreak--,tx_gridelements_title',
    'after:subheader'
);
