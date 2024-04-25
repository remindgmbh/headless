<?php

declare(strict_types=1);

namespace Remind\Headless\DataProcessing;

use FriendsOfTYPO3\Headless\DataProcessing\FilesProcessor as BaseFilesProcessor;

class FilesProcessor extends BaseFilesProcessor
{
    protected function processFiles(array $properties = []): ?array
    {
        $data = parent::processFiles($properties);
        if ($data) {
            if ((bool) ($properties['returnFlattenObject'] ?? false)) {
                unset($data['properties']['crop']);
                unset($data['properties']['cropDimensions']);
            } else {
                foreach ($data as &$processedFile) {
                    unset($processedFile['properties']['crop']);
                    unset($processedFile['properties']['cropDimensions']);
                }
            }
        }
        return $data;
    }
}
