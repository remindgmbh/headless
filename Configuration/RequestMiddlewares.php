<?php

use Remind\Headless\Middleware\ImageProcessingMiddleware;

return [
    'frontend' => [
        'rmnd_headless/imageprocessing' => [
            'target' => ImageProcessingMiddleware::class,
            'after' => [
                'typo3/cms-frontend/site',
            ],
            'before' => [
                'typo3/cms-frontend/backend-user-authentication',
            ],
        ],
    ],
];
