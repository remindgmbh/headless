<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die;

ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
        'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ce.xlf:header_slider.title',
        'header_slider',
        'content-carousel-item-textandimage',
    ],
    'header',
    'after'
);

ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:rmnd_headless/Configuration/FlexForms/HeaderSlider.xml',
    'header_slider'
);

$GLOBALS['TCA']['tt_content']['types']['header_slider'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,
            header,
            pi_flexform,
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
                                    bodytext;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext_formlabel,
                                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.images,
                                    image,
                                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                                    --palette--;;hidden,
                                    --palette--;;access,
                                --palette--;;hiddenLanguagePalette,
                            ',
                        ],
                    ],
                    'columns' => [
                        'image' => [
                            'config' => [
                                'maxitems' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
