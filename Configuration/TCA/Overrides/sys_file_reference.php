<?php

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3_MODE') || die;

ExtensionManagementUtility::addTCAcolumns('sys_file_reference', [
    'tx_headless_lazy_loading' => [
        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_file.xlf:lazy_loading',
        'description' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_file.xlf:lazy_loading.description',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'default' => 0,
        ],
    ],
]);

$GLOBALS
    ['TCA']
    ['sys_file_reference']
    ['types']
    [File::FILETYPE_IMAGE]
    ['columnsOverrides']
    ['tx_headless_lazy_loading']
    ['config']
    ['default'] = 1;

ExtensionManagementUtility::addFieldsToPalette(
    'sys_file_reference',
    'imageoverlayPalette',
    'tx_headless_lazy_loading'
);

ExtensionManagementUtility::addFieldsToPalette(
    'sys_file_reference',
    'imageoverlayPalette',
    'tx_headless_lazy_loading'
);

ExtensionManagementUtility::addFieldsToPalette(
    'sys_file_reference',
    'videoOverlayPalette',
    'tx_headless_lazy_loading'
);
