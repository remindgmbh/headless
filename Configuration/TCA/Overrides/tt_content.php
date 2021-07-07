<?php
defined('TYPO3_MODE') || die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'tt_content',
    [
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
        ],
        'background_wide' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/Backend.xlf:tt_content.background_wide',
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
    'background_wide,--linebreak--',
    'after:background_color'
);
