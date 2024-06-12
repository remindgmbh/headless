<?php

declare(strict_types=1);

namespace Remind\Headless\Event\Listener;

use FriendsOfTYPO3\Headless\Event\EnrichFileDataEvent;

class EnrichFileDataEventListener
{
    public function __invoke(EnrichFileDataEvent $event): void
    {
        $originalFile = $event->getOriginal();
        $processedFile = $event->getProcessed();
        $properties = $event->getProperties();
        $processingConfiguration = $event->getProcessingConfiguration();

        // class ProcessingConfiguration always inserts arrays for these defaultFields, so the following checks shouldnt be necessary, but check anyway
        $defaultFieldsByType = $processingConfiguration->defaultFieldsByType ?? [];
        $defaultImageFields = array_merge($defaultFieldsByType, $processingConfiguration->defaultImageFields ?? []);
        $defaultVideoFields = array_merge($defaultFieldsByType, $processingConfiguration->defaultVideoFields ?? []);

        $defaultFields = match ($properties['type']) {
            'image' => $defaultImageFields,
            'video' => $defaultVideoFields,
            default => $defaultFieldsByType,
        };

        // processedFile has to be used here instead of originalFile, otherwise pre processed attributes (like extension = svg) get readded
        foreach ($defaultFields as $field) {
            if ($processedFile->hasProperty($field) && !isset($properties[$field])) {
                $properties[$field] = $processedFile->getProperty($field);
            }
        }

        $properties['lazyLoading'] = (bool) $originalFile->getProperty('tx_headless_lazy_loading') ?? true;
        $event->setProperties($properties);
    }
}
