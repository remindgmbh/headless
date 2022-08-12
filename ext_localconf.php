<?php

declare(strict_types=1);

defined('TYPO3') or die();

(function () {
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
        ['SYS']
        ['Objects']
        [ApacheSolrForTypo3\Solr\Controller\SearchController::class] = 
            ['className' => \Remind\Typo3Headless\XClass\Controller\SolrSearchController::class];
})();

