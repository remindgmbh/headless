<?php

declare(strict_types=1);

namespace Remind\Headless\BreadcrumbTitle;

use TYPO3\CMS\Core\SingletonInterface;

class AbstractBreadcrumbTitleProvider implements BreadcrumbTitleProviderInterface, SingletonInterface
{
    protected $title = '';

    public function getTitle(): string
    {
        return $this->title;
    }
}
