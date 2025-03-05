<?php

declare(strict_types=1);

namespace Remind\Headless\BreadcrumbTitle;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\SingletonInterface;

class AbstractBreadcrumbTitleProvider implements BreadcrumbTitleProviderInterface, SingletonInterface
{
    protected ServerRequestInterface $request;

    protected string $title = '';

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
