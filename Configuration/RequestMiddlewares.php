<?php

return [
    'frontend' => [
        'rmnd_headless/imageprocessing' => [
            'target' => Remind\Headless\Middleware\ImageProcessingMiddleware::class,
            'after' => [
                'typo3/cms-frontend/site',
            ],
            'before' => [
                'typo3/cms-frontend/backend-user-authentication',
            ],
        ],
    ],
];
