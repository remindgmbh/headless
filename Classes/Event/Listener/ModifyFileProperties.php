<?php

declare(strict_types=1);

namespace Remind\Headless\Event\Listener;

use Remind\Headless\Event\ModifyFilePropertiesEvent;

class ModifyFileProperties
{
    public function __invoke(ModifyFilePropertiesEvent $event): void
    {
        $metaData = $event->getMetaData();
        $properties = $event->getProperties();
        $properties['lazyLoading'] = (bool) $metaData['tx_headless_lazy_loading'] ?? true;
        $event->setProperties($properties);
    }
}
