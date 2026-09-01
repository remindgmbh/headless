<?php

declare(strict_types=1);

namespace Remind\Headless\Event\Listener;

use FriendsOfTYPO3\Headless\Event\EnrichFileDataEvent;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class EnrichFileDataEventListener
{
    public function __invoke(EnrichFileDataEvent $event): void
    {
        $originalFile = $event->getOriginal();
        $properties = $event->getProperties();
        $processingConfiguration = $event->getProcessingConfiguration();

        $defaultFields = $processingConfiguration->defaultFieldsByType ?? [];
        $imageFields = array_merge($defaultFields, $processingConfiguration->defaultImageFields ?? []);
        $videoFields = array_merge($defaultFields, $processingConfiguration->defaultVideoFields ?? []);

        $fields = match ($properties['type']) {
            'image' => $imageFields,
            'video' => $videoFields,
            default => $defaultFields,
        };

        foreach ($fields as $field) {
            $as = $field;
            if (str_contains($field, ' as ')) {
                [$field, $as] = GeneralUtility::trimExplode(' as ', $field, true);
            }
            if (
                $originalFile->hasProperty($field) &&
                !array_key_exists($as, $properties)
            ) {
                $properties[$as] = $originalFile->getProperty($field);
            }
        }

        /**
         * Crop Variants with non-empty crop areas of SVG Images are converted to SVG
         * so these images have to be treated different than pure SVGs in frontend
         */

        if ($originalFile->getExtension() === 'svg') {
            $crop = $originalFile->getProperty('crop');
            $cropVariantCollection = CropVariantCollection::create($crop);
            $cropVariants = empty($crop) ? [] : array_keys(json_decode($crop, true));
            foreach ($cropVariants as $cropVariant) {
                $cropArea = $cropVariantCollection->getCropArea((string) $cropVariant);
                if (!$cropArea->isEmpty()) {
                    $properties['extension'] = 'mixed';
                    break;
                }
            }
        }

        $event->setProperties($properties);
    }
}
