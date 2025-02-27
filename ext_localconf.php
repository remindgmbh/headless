<?php

declare(strict_types=1);

use Remind\Headless\LinkHandler\CookiesLinkBuilder;
use Remind\Headless\LinkHandler\CookiesLinkHandling;

defined('TYPO3') or die;

(function (): void {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['headless.elementBodyResponse'] = true;

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['headless.frontendUrls'] = true;

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['EXT:headless/Resources/Private/Language/locallang.xlf'][] = 'EXT:rmnd_headless/Resources/Private/Language/Overrides/locallang_headless.xlf';

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['de']['EXT:headless/Resources/Private/Language/de.locallang.xlf'][] = 'EXT:rmnd_headless/Resources/Private/Language/Overrides/de.locallang_headless.xlf';

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['EXT:felogin/Resources/Private/Language/locallang.xlf'][] = 'EXT:rmnd_headless/Resources/Private/Language/Overrides/locallang_felogin.xlf';

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['de']['EXT:felogin/Resources/Private/Language/de.locallang.xlf'][] = 'EXT:rmnd_headless/Resources/Private/Language/Overrides/de.locallang_felogin.xlf';

    $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['default'] = 'EXT:rmnd_headless/Configuration/RTE/Default.yaml';

    $GLOBALS['TYPO3_CONF_VARS']['FE']['typolinkBuilder']['cookies'] = CookiesLinkBuilder::class;

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['linkHandler']['cookies'] = CookiesLinkHandling::class;
})();
