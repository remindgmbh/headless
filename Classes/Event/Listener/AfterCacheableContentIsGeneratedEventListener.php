<?php

declare(strict_types=1);

namespace Remind\Headless\Event\Listener;

use FriendsOfTYPO3\Headless\Json\JsonEncoder;
use Remind\Headless\BreadcrumbTitle\BreadcrumbTitleProviderManager;
use Throwable;
use TYPO3\CMS\Frontend\Event\AfterCacheableContentIsGeneratedEvent;

class AfterCacheableContentIsGeneratedEventListener
{
    public function __construct(
        private readonly JsonEncoder $encoder,
        private readonly BreadcrumbTitleProviderManager $breadcrumbTitleProviderManager,
    ) {
    }

    public function __invoke(AfterCacheableContentIsGeneratedEvent $event): void
    {
        try {
            $content = json_decode($event->getController()->content, true, 512, JSON_THROW_ON_ERROR);

            $breadcrumbTitle = $this->breadcrumbTitleProviderManager->getTitle($event->getRequest());

            if ($breadcrumbTitle) {
                $content['breadcrumbs'][array_key_last($content['breadcrumbs'])]['title'] = $breadcrumbTitle;
            }

            $event->getController()->content = $this->encoder->encode($content);
        } catch (Throwable) {
            return;
        }
    }
}
