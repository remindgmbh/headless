<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die;

ExtensionManagementUtility::addTCAcolumns(
    'pages',
    [
        'tx_headless_breadcrumbs_background_color' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_pages.xlf:breadcrumbs_background_color',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_pages.xlf:breadcrumbs_background_color.none',
                        'value' => null,
                    ],
                ],
                'default' => null,
            ],
        ],
        'tx_headless_config' => [
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_pages.xlf:page_config',
            'config' => [
                'type' => 'flex',
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
                'ds' => [
                    'default' => 'FILE:EXT:rmnd_headless/Configuration/FlexForms/Empty.xml',
                ],
            ],
            'displayCond' => 'FIELD:is_siteroot:REQ:true',
        ],
        'tx_headless_overview_label' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_pages.xlf:overview_label',
            'config' => [
                'type' => 'input',
            ],
        ],
    ]
);

ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'layout',
    '--linebreak--,tx_headless_breadcrumbs_background_color',
);

ExtensionManagementUtility::addFieldsToPalette(
    'pages',
    'title',
    '--linebreak--,tx_headless_overview_label',
);

ExtensionManagementUtility::addToAllTCAtypes(
    'pages',
    'tx_headless_config',
);
