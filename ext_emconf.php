<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'REMIND - Headless Extension',
    'description' => 'This extension contains default content elements and layout definitions for TYPO3 Headless',
    'category' => 'fe',
    'version' => '1.0.0',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'author' => 'David Mellen',
    'author_company' => 'REMIND GmbH',
    'author_email' => 'd.mellen@remind.de',
    'constraints' => [
        'depends' => [
            'frontend' => '11.5.0-11.5.99',
            'typo3' => '11.5.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
