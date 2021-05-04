<?php
defined('TYPO3_MODE') || die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'tt_content',
    [
        'background_class' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/Backend.xlf:tt_content.background_class',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/Backend.xlf:tt_content.background_class.none', ''],
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/Backend.xlf:tt_content.background_class.primary', 'primary'],
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/Backend.xlf:tt_content.background_class.secondary', 'secondary'],
                    ['LLL:EXT:rmnd_headless/Resources/Private/Language/Backend.xlf:tt_content.background_class.accent', 'accent'],
                ],
                'default' => ''
            ],
        ],
    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'frames',
    'background_class',
    'after:frame_class'
);
