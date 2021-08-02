<?php
defined('TYPO3_MODE') || die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
    'rmnd_headless',
    'Configuration/TSConfig/Page/Mod/WebLayout/BackendLayouts.tsconfig',
    'RMND Headless: Backend Layouts'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
    'rmnd_headless',
    'Configuration/TSConfig/Page/TCEFORM.tsconfig',
    'RMND Headless: TCE Form'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
    'rmnd_headless',
    'Configuration/TSConfig/Page/tx_gridelements.tsconfig',
    'RMND Headless: Grid Elements'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
    'rmnd_headless',
    'Configuration/TSConfig/Page/Mod/Wizards/NewContentElement/WizardItems.tsconfig',
    'RMND Headless: Wizard Items'
);