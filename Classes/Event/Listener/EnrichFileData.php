<?php

declare(strict_types=1);

namespace Remind\Headless\Event\Listener;

use FriendsOfTYPO3\Headless\Event\EnrichFileDataEvent;

class EnrichFileData
{
    public function __invoke(EnrichFileDataEvent $event): void
    {
        $originalFile = $event->getOriginal();
        $properties = $event->getProperties();
        $properties['lazyLoading'] = (bool) $originalFile->getProperty('tx_headless_lazy_loading') ?? true;
        $event->setProperties($properties);
    }
}
