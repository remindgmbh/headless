<?php

declare(strict_types=1);

namespace Remind\Headless\BreadcrumbTitle;

interface BreadcrumbTitleProviderInterface
{
    public function getTitle(): string;
}
