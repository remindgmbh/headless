<?php

declare(strict_types=1);

use Remind\Headless\Middleware\AssetMiddleware;

return [
    'frontend' => [
        'rmnd_headless/asset' => [
            'after' => [
                'typo3/cms-frontend/site',
            ],
            'before' => [
                'typo3/cms-frontend/backend-user-authentication',
            ],
            'target' => AssetMiddleware::class,
        ],
    ],
];
