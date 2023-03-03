<?php

declare(strict_types=1);

use Remind\Headless\Hooks\FlexFormTools as FlexFormToolsHooks;
use Remind\Headless\XClass\DataStructureIdentifierHook;
use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Hooks\DataStructureIdentifierHook as BaseDataStructureIdentifierHook;

defined('TYPO3') or die;

(function () {
    $versionInformation = GeneralUtility::makeInstance(Typo3Version::class);
    // Only include page.tsconfig if TYPO3 version is below 12 so that it is not imported twice.
    if ($versionInformation->getMajorVersion() < 12) {
        ExtensionManagementUtility::addPageTSConfig('
          @import "EXT:rmnd_headless/Configuration/page.tsconfig"
       ');
    }

    /* @var $iconRegistry \TYPO3\CMS\Core\Imaging\IconRegistry */
    $iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);

    $iconRegistry->registerIcon(
        'content-footer',
        SvgIconProvider::class,
        ['source' => 'EXT:rmnd_headless/Resources/Public/Icons/content-footer.svg']
    );

    $GLOBALS
        ['TYPO3_CONF_VARS']
        ['SYS']['features']
        ['headless.elementBodyResponse'] = true;

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

    $GLOBALS
        ['TYPO3_CONF_VARS']
        ['SC_OPTIONS']
        [FlexFormTools::class]
        ['flexParsing']
        [FlexFormToolsHooks::class] = FlexFormToolsHooks::class;

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][BaseDataStructureIdentifierHook::class] = [
        'className' => DataStructureIdentifierHook::class,
    ];
})();
