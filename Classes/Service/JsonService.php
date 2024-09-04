<?php

declare(strict_types=1);

namespace Remind\Headless\Service;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Pagination\PaginationInterface;
use TYPO3\CMS\Extbase\Mvc\Web\RequestBuilder;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

class JsonService
{
    public function __construct(
        private readonly UriBuilder $uriBuilder,
        RequestBuilder $requestBuilder,
    ) {
        $extbaseRequest = $requestBuilder->build($this->getRequest());
        $this->uriBuilder->setRequest($extbaseRequest);
    }

    /**
     * @return mixed[]
     */
    public function serializePagination(PaginationInterface $pagination, string $queryParam, int $currentPage): array
    {
        $firstPageNumber = $pagination->getFirstPageNumber();
        $lastPageNumber = $pagination->getLastPageNumber();
        $previousPageNumber = $pagination->getPreviousPageNumber();
        $nextPageNumber = $pagination->getNextPageNumber();

        $first = $this->uriBuilder
            ->reset()
            ->setAddQueryString('untrusted')
            ->uriFor(null, [$queryParam => $firstPageNumber]);

        $last = $this->uriBuilder
            ->reset()
            ->setAddQueryString('untrusted')
            ->uriFor(null, [$queryParam => $lastPageNumber]);

        if (
            $previousPageNumber &&
            $previousPageNumber >= $firstPageNumber
        ) {
            $prev = $this->uriBuilder
                ->reset()
                ->setAddQueryString('untrusted')
                ->uriFor(null, [$queryParam => $previousPageNumber]);
        }

        if (
            $nextPageNumber &&
            $nextPageNumber <= $lastPageNumber
        ) {
            $next = $this->uriBuilder
                ->reset()
                ->setAddQueryString('untrusted')
                ->uriFor(null, [$queryParam => $nextPageNumber]);
        }

        $pages = [];

        for ($page = $firstPageNumber; $page <= $lastPageNumber; $page++) {
            $link = $this->uriBuilder
                ->reset()
                ->setAddQueryString('untrusted')
                ->uriFor(null, [$queryParam => $page]);

            $pages[] = [
                'active' => $page === $currentPage,
                'link' => $link,
                'pageNumber' => $page,
            ];
        }

        $result = [
            'endRecordNumber' => $pagination->getEndRecordNumber(),
            'first' => $first,
            'last' => $last,
            'next' => $next ?? null,
            'pages' => $pages,
            'prev' => $prev ?? null,
            'startRecordNumber' => $pagination->getStartRecordNumber(),
        ];

        return $result;
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
