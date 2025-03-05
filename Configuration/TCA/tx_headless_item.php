<?php

declare(strict_types=1);

defined('TYPO3') || die;

return [
    'columns' => [
        'bodytext' => [
            'config' => [
                'cols' => 80,
                'enableRichtext' => true,
                'rows' => 10,
                'softref' => 'typolink_tag,email[subst],url',
                'type' => 'text',
            ],
            'l10n_mode' => 'prefixLangTitle',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.text',
        ],
        'flexform' => [
            'config' => [
                'ds' => [
                    'default' => '
                        <T3DataStructure>
                            <ROOT>
                                <type>array</type>
                                <el>
                                    <warning>
                                        <label>LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_item.xlf:columns.flexform.warning.label</label>
                                        <description>LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_item.xlf:columns.flexform.warning.default</description>
                                        <config>
                                            <type>none</type>
                                            <size>0</size>
                                        </config>
                                    </warning>
                                </el>
                            </ROOT>
                        </T3DataStructure>
                    ',
                ],
                // FlexForm according to tt_content:CType is selected in Remind\Headless\Event\Listener\AfterFlexFormDataStructureIdentifierInitializedEventListener
                'type' => 'flex',
            ],
            'l10n_display' => 'hideDiff',
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_item.xlf:columns.flexform',
        ],
        'foreign_table' => [
            'config' => [
                'eval' => 'trim',
                'size' => 30,
                'type' => 'input',
            ],
            'l10n_mode' => 'exclude',
        ],
        'foreign_uid' => [
            'config' => [
                'allowed' => '',
                'maxitems' => 1,
                'minitems' => 0,
                'size' => 1,
                'type' => 'group',
            ],
            'exclude' => true,
        ],
        'header' => [
            'config' => [
                'max' => 256,
                'size' => 50,
                'type' => 'input',
            ],
            'l10n_cat' => 'text',
            'l10n_mode' => 'prefixLangTitle',
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header',
        ],
        'header_layout' => [
            'config' => [
                'default' => 0,
                'items' => [
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_item.xlf:columns.header_layout.text',
                        'value' => '0',
                    ],
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_item.xlf:columns.header_layout.h1',
                        'value' => '1',
                    ],
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_item.xlf:columns.header_layout.h2',
                        'value' => '2',
                    ],
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_item.xlf:columns.header_layout.h3',
                        'value' => '3',
                    ],
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_item.xlf:columns.header_layout.h4',
                        'value' => '4',
                    ],
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_item.xlf:columns.header_layout.h5',
                        'value' => '5',
                    ],
                    [
                        'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_item.xlf:columns.header_layout.h6',
                        'value' => '6',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_layout.I.6',
                        'value' => '100',
                    ],
                ],
                'renderType' => 'selectSingle',
                'type' => 'select',
            ],
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.type',
        ],
        'header_link' => [
            'config' => [
                'size' => 50,
                'type' => 'link',
            ],
            'exclude' => true,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_link',
        ],
        'header_position' => [
            'config' => [
                'default' => '',
                'items' => [
                    [
                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.default_value',
                        'value' => '',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_position.I.1',
                        'value' => 'center',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_position.I.2',
                        'value' => 'right',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_position.I.3',
                        'value' => 'left',
                    ],
                ],
                'renderType' => 'selectSingle',
                'type' => 'select',
            ],
            'exclude' => true,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_position',
        ],
        'image' => [
            'config' => [
                'allowed' => 'common-image-types',
                'type' => 'file',
            ],
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.images',
        ],
        'space_after_class' => [
            'config' => [
                'default' => '',
                'items' => [
                    [
                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.default_value',
                        'value' => '',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_extra_small',
                        'value' => 'extra-small',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_small',
                        'value' => 'small',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_medium',
                        'value' => 'medium',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_large',
                        'value' => 'large',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_extra_large',
                        'value' => 'extra-large',
                    ],
                ],
                'renderType' => 'selectSingle',
                'type' => 'select',
            ],
            'exclude' => true,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_after_class',
        ],
        'space_before_class' => [
            'config' => [
                'default' => '',
                'items' => [
                    [
                        'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.default_value',
                        'value' => '',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_extra_small',
                        'value' => 'extra-small',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_small',
                        'value' => 'small',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_medium',
                        'value' => 'medium',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_large',
                        'value' => 'large',
                    ],
                    [
                        'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_class_extra_large',
                        'value' => 'extra-large',
                    ],
                ],
                'renderType' => 'selectSingle',
                'type' => 'select',
            ],
            'exclude' => true,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_before_class',
        ],
        'subheader' => [
            'config' => [
                'max' => 256,
                'size' => 50,
                'softref' => 'email[subst]',
                'type' => 'input',
            ],
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.subheader',
        ],
        'title' => [
            'config' => [
                'max' => 256,
                'size' => 50,
                'type' => 'input',
            ],
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_item.xlf:columns.title',
        ],
        'tx_headless_item' => [
            'config' => [
                'foreign_field' => 'foreign_uid',
                'foreign_table' => 'tx_headless_item',
                'foreign_table_field' => 'foreign_table',
                'type' => 'inline',
            ],
            'exclude' => 0,
            'label' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:tx_headless_item',
        ],
    ],
    'ctrl' => [
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'endtime' => 'endtime',
            'starttime' => 'starttime',
        ],
        'hideTable' => true,
        'iconfile' => 'EXT:core/Resources/Public/Icons/T3Icons/svgs/actions/actions-folder.svg',
        'label' => 'header',
        'label_alt' => 'subheader,bodytext',
        'languageField' => 'sys_language_uid',
        'searchFields' => 'header,subheader,bodytext',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
        'sortby' => 'sorting',
        'title' => 'LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_item.xlf:title',
        'translationSource' => 'l10n_source',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'transOrigPointerField' => 'l10n_parent',
        'tstamp' => 'tstamp',
        'versioningWS' => true,
    ],
    'palettes' => [

        'access' => [
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access',
            'showitem' => '
                    starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel,
                    endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel,
                ',
        ],
        'frames' => [
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames',
            'showitem' => '
                space_before_class;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_before_class_formlabel,
                space_after_class;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:space_after_class_formlabel,
            ',
        ],
        'header' => [
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.headers',
            'showitem' => '
                header;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_formlabel,
                --linebreak--,
                header_layout;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_layout_formlabel,
                header_position;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_position_formlabel,
                --linebreak--,
                header_link;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_link_formlabel,
            ',
        ],

        'headers' => [
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.headers',
            'showitem' => '
                header;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_formlabel,
                --linebreak--,
                header_layout;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_layout_formlabel,
                header_position;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_position_formlabel,
                --linebreak--,
                header_link;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_link_formlabel,
                --linebreak--,
                subheader;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:subheader_formlabel
            ',
        ],

        'hidden' => [
            'showitem' => '
                hidden;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:field.default.hidden
            ',
        ],

        // hidden but needs to be included all the time, so sys_language_uid is set correctly
        'hiddenLanguagePalette' => [
            'isHiddenPalette' => true,
            'showitem' => 'sys_language_uid, l10n_parent',
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    --palette--;;headers,
                    title,
                    bodytext;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext_formlabel,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.images,
                    image,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
                    --palette--;;frames,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    --palette--;;hidden,
                    --palette--;;access,
                --palette--;;hiddenLanguagePalette,
            ',
        ],
    ],
];
