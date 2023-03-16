<?php

declare(strict_types=1);

namespace Remind\Headless\Service;

use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Pagination\PaginationInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\Web\RequestBuilder;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

class JsonService
{
    private array $settings = [];

    public function __construct(
        private readonly UriBuilder $uriBuilder,
        private readonly LoggerInterface $logger,
        Request $request,
        RequestBuilder $requestBuilder,
        ConfigurationManagerInterface $configurationManager
    ) {
        $extbaseRequest = $requestBuilder->build($request);
        $this->uriBuilder->setRequest($extbaseRequest);
        $this->settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );
    }

    public function serializePagination(PaginationInterface $pagination, string $queryParam, int $currentPage): array
    {
        $firstPageNumber = $pagination->getFirstPageNumber();
        $lastPageNumber = $pagination->getLastPageNumber();
        $previousPageNumber = $pagination->getPreviousPageNumber();
        $nextPageNumber = $pagination->getNextPageNumber();

        $first = $this->uriBuilder
            ->reset()
            ->setAddQueryString(true)
            ->uriFor(null, [$queryParam => $firstPageNumber]);

        $last = $this->uriBuilder
            ->reset()
            ->setAddQueryString(true)
            ->uriFor(null, [$queryParam => $lastPageNumber]);

        if ($previousPageNumber && $previousPageNumber >= $firstPageNumber) {
            $prev = $this->uriBuilder
                ->reset()
                ->setAddQueryString(true)
                ->uriFor(null, [$queryParam => $previousPageNumber]);
        }

        if ($nextPageNumber && $nextPageNumber <= $lastPageNumber) {
            $next = $this->uriBuilder
                ->reset()
                ->setAddQueryString(true)
                ->uriFor(null, [$queryParam => $nextPageNumber]);
        }

        $pages = [];

        for ($page = $firstPageNumber; $page <= $lastPageNumber; $page++) {
            $link = $this->uriBuilder
                ->reset()
                ->setAddQueryString(true)
                ->uriFor(null, [$queryParam => $page]);

            $pages[] = [
                'pageNumber' => $page,
                'link' => $link,
                'current' => $page === $currentPage,
            ];
        }

        $result = [
            'first' => $first,
            'last' => $last,
            'prev' => $prev ?? null,
            'next' => $next ?? null,
            'pages' => $pages,
        ];

        return $result;
    }
}
