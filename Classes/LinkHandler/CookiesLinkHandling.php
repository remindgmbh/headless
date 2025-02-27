<?php

declare(strict_types=1);

namespace Remind\Headless\LinkHandler;

use TYPO3\CMS\Core\LinkHandling\LinkHandlingInterface;

class CookiesLinkHandling implements LinkHandlingInterface
{
    protected string $baseUrn = 't3://cookies';

    /**
     * @param mixed[] $parameters
     */
    public function asString(array $parameters): string
    {
        return $this->baseUrn . '?action=' . $parameters['action'];
    }

    /**
     * @param mixed[] $data
     * @return mixed[]
     */
    public function resolveHandlerData(array $data): array
    {
        return [
            'action' => $data['action'] ?? '',
        ];
    }
}
