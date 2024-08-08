<?php

declare(strict_types=1);

defined('TYPO3') || die;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addStaticFile(
    'rmnd_headless',
    'Configuration/TypoScript',
    'REMIND - Headless'
);
