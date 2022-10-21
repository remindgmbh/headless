<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3_MODE') || die;

ExtensionManagementUtility::addTCAcolumns(
    'pages',
    [
        'tx_headless_overview_label' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_pages.xlf:tx_headless_overview_label',
            'config' => [
                'type' => 'input',
            ],
        ],
    ]
);

ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'title',
    '--linebreak--,tx_headless_overview_label',
);
