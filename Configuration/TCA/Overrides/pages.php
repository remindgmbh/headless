<?php
defined('TYPO3_MODE') || die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
    'rmnd_headless_be_content',
    'Configuration/TSConfig/Page/Mod/WebLayout/BackendLayouts.tsconfig',
    'RMND Headless Content: Backend Layouts'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
    'rmnd_headless_be_content',
    'Configuration/TSConfig/Page/TCEFORM.tsconfig',
    'RMND Headless Content: TCE Form'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
    'rmnd_headless_be_content',
    'Configuration/TSConfig/Page/tx_gridelements.tsconfig',
    'RMND Headless Content: Grid Elements'
);