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
                ],
                'default' => ''
            ],
        ],
    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'frames',
    '--linebreak--,background_color',
    'after:frame_class'
);
