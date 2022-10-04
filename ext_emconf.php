<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Remind Headless',
    'description' => 'This extension contains default content elements and layout definitions for TYPO3 Headless',
    'category' => 'plugin',
    'version' => '1.0.0',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'author' => 'David Mellen',
    'author_company' => 'REMIND GmbH',
    'author_email' => 'd.mellen@remind.de',
    'constraints' => [
        'depends' => [
            'rmnd_content' => 'dev-main',
        ],
    ],
];
