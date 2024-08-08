<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die;

ExtensionManagementUtility::addTCAcolumns(
    'pages',
    [
        'tx_headless_breadcrumbs_background_color' => [
            'config' => [
                'default' => null,
                'items' => [
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_pages.xlf:breadcrumbs_background_color.none',
                        'value' => null,
                    ],
                ],
                'renderType' => 'selectSingle',
                'type' => 'select',
            ],
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_pages.xlf:breadcrumbs_background_color',
        ],
        'tx_headless_config' => [
            'config' => [
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
                'ds' => [
                    'default' => 'FILE:EXT:rmnd_headless/Configuration/FlexForms/Empty.xml',
                ],
                'type' => 'flex',
            ],
            'displayCond' => 'FIELD:is_siteroot:REQ:true',
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_pages.xlf:page_config',
        ],
        'tx_headless_overview_label' => [
            'config' => [
                'type' => 'input',
            ],
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_pages.xlf:overview_label',
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
