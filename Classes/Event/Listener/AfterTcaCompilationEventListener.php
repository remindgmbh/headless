<?php

declare(strict_types=1);

namespace Remind\Headless\Event\Listener;

use TYPO3\CMS\Core\Configuration\Event\AfterTcaCompilationEvent;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class AfterTcaCompilationEventListener
{
    public function __invoke(AfterTcaCompilationEvent $event): void
    {
        /**
         * Adding palette here makes sure all content elements,
         * even the ones added in extensions after this one, are included
         */
        ExtensionManagementUtility::addToAllTCAtypes(
            'tt_content',
            '--div--;LLL:EXT:rmnd_headless/Resources/Private/Language/locallang_ttc.xlf:cookies,--palette--;;cookies',
            '',
            'after:rowDescription'
        );
        $event->setTca($GLOBALS['TCA']);
    }
}
