<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3_MODE') || die;

ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    'bodytext',
    'form_formframework',
    'after:palette:headers'
);

$GLOBALS['TCA']['tt_content']['types']['form_formframework'] = array_replace_recursive(
    $GLOBALS['TCA']['tt_content']['types']['form_formframework'],
    [
        'columnsOverrides' => [
            'bodytext' => [
                'config' => [
                    'enableRichtext' => true,
                ],
            ],
        ],
    ],
);
