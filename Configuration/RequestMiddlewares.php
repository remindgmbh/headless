<?php

use Remind\Headless\Middleware\AssetMiddleware;

return [
    'frontend' => [
        'rmnd_headless/asset' => [
            'target' => AssetMiddleware::class,
            'after' => [
                'typo3/cms-frontend/site',
            ],
            'before' => [
                'typo3/cms-frontend/backend-user-authentication',
            ],
        ],
    ],
];
