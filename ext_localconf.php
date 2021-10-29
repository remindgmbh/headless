<?php
defined('TYPO3_MODE') || die;

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('@import "EXT:rmnd_headless/Configuration/TSConfig/Page/*.tsconfig"');

$GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['headless.elementBodyResponse'] = true;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['EXT:headless/Resources/Private/Language/locallang.xlf'][] = 'EXT:rmnd_headless/Resources/Private/Language/Overrides/locallang_headless.xlf';
$GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['de']['EXT:headless/Resources/Private/Language/de.locallang.xlf'][] = 'EXT:rmnd_headless/Resources/Private/Language/Overrides/de.locallang_headless.xlf';
$GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['EXT:felogin/Resources/Private/Language/locallang.xlf'][] = 'EXT:rmnd_headless/Resources/Private/Language/Overrides/locallang_felogin.xlf';
$GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['de']['EXT:felogin/Resources/Private/Language/de.locallang.xlf'][] = 'EXT:rmnd_headless/Resources/Private/Language/Overrides/de.locallang_felogin.xlf';

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][ApacheSolrForTypo3\Solr\Controller\SearchController::class] = [
    'className' => \Remind\Headless\XClass\Controller\SolrSearchController::class
];
