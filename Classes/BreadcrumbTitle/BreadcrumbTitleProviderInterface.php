<?php

declare(strict_types=1);

namespace Remind\Headless\BreadcrumbTitle;

use Psr\Http\Message\ServerRequestInterface;

interface BreadcrumbTitleProviderInterface
{
    public function getTitle(): string;

    public function setRequest(ServerRequestInterface $request): void;
}
