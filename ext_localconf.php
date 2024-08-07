<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

defined('TYPO3') or die;

(function () {
    /* @var $iconRegistry \TYPO3\CMS\Core\Imaging\IconRegistry */
    $iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);

    $iconRegistry->registerIcon(
        'content-footer',
        SvgIconProvider::class,
        ['source' => 'EXT:rmnd_headless/Resources/Public/Icons/content-footer.svg']
    );

    $GLOBALS
        ['TYPO3_CONF_VARS']
        ['SYS']
        ['features']
        ['headless.elementBodyResponse'] = true;

    $GLOBALS
        ['TYPO3_CONF_VARS']
        ['SYS']
        ['features']
        ['headless.frontendUrls'] = true;

    $GLOBALS
        ['TYPO3_CONF_VARS']
        ['SYS']
        ['locallangXMLOverride']
        ['EXT:headless/Resources/Private/Language/locallang.xlf']
        [] = 'EXT:rmnd_headless/Resources/Private/Language/Overrides/locallang_headless.xlf';

    $GLOBALS
        ['TYPO3_CONF_VARS']
        ['SYS']
        ['locallangXMLOverride']
        ['de']
        ['EXT:headless/Resources/Private/Language/de.locallang.xlf']
        [] = 'EXT:rmnd_headless/Resources/Private/Language/Overrides/de.locallang_headless.xlf';

    $GLOBALS
        ['TYPO3_CONF_VARS']
        ['SYS']
        ['locallangXMLOverride']
        ['EXT:felogin/Resources/Private/Language/locallang.xlf']
        [] = 'EXT:rmnd_headless/Resources/Private/Language/Overrides/locallang_felogin.xlf';

    $GLOBALS
        ['TYPO3_CONF_VARS']
        ['SYS']
        ['locallangXMLOverride']
        ['de']
        ['EXT:felogin/Resources/Private/Language/de.locallang.xlf']
        [] = 'EXT:rmnd_headless/Resources/Private/Language/Overrides/de.locallang_felogin.xlf';

    $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['default'] = 'EXT:rmnd_headless/Configuration/RTE/Default.yaml';
})();
