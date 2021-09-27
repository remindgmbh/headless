<?php
defined('TYPO3_MODE') || die;

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'rmnd_headless',
    'Configuration/TypoScript',
    'Remind Headless'
);
