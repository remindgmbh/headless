<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die;

ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ce.xlf:tabs.title',
        'value' => 'tabs',
        'icon' => 'content-tab',
        'group' => 'default',
    ],
);

$GLOBALS['TCA']['tt_content']['types']['tabs'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,
            --palette--;;headers,
            tx_headless_item,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
            --palette--;;frames,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
            --palette--;;language,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,
            --palette--;;access,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
            categories,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
            rowDescription,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
    ',
    'columnsOverrides' => [
        'tx_headless_item' => [
            'config' => [
                'overrideChildTca' => [
                    'types' => [
                        '0' => [
                            'showitem' => '
                                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                                    --palette--;;headers,
                                    title;LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ce.xlf:tabs.columns.title,
                                    bodytext;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext_formlabel,
                                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
                                    --palette--;;frames,
                                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                                    --palette--;;hidden,
                                    --palette--;;access,
                                --palette--;;hiddenLanguagePalette,
                            ',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
