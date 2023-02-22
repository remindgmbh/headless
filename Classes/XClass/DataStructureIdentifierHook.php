<?php

declare(strict_types=1);

namespace Remind\Headless\XClass;

use TYPO3\CMS\Form\Hooks\DataStructureIdentifierHook as BaseDataStructureIdentifierHook;

/**
 * Workaround for https://forge.typo3.org/issues/97972 since local patches from packages don't work
 * (see https://github.com/cweagans/composer-patches/issues/339)
 */
class DataStructureIdentifierHook extends BaseDataStructureIdentifierHook
{
    public function __construct()
    {
        if (isset($GLOBALS['LANG'])) {
            parent::__construct();
        }
    }
}
