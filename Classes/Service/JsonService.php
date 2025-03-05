<?php

declare(strict_types=1);

namespace Remind\Headless\Service;

use TYPO3\CMS\Core\Pagination\PaginationInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

class JsonService
{
    /**
     * @return mixed[]
     */
    public function serializePagination(
        UriBuilder $uriBuilder,
        PaginationInterface $pagination,
        string $queryParam,
        int $currentPage
    ): array {
        $firstPageNumber = $pagination->getFirstPageNumber();
        $lastPageNumber = $pagination->getLastPageNumber();
        $previousPageNumber = $pagination->getPreviousPageNumber();
        $nextPageNumber = $pagination->getNextPageNumber();

        $first = $uriBuilder
            ->reset()
            ->setAddQueryString('untrusted')
            ->uriFor(null, [$queryParam => $firstPageNumber]);

        $last = $uriBuilder
            ->reset()
            ->setAddQueryString('untrusted')
            ->uriFor(null, [$queryParam => $lastPageNumber]);

        if (
            $previousPageNumber &&
            $previousPageNumber >= $firstPageNumber
        ) {
            $prev = $uriBuilder
                ->reset()
                ->setAddQueryString('untrusted')
                ->uriFor(null, [$queryParam => $previousPageNumber]);
        }

        if (
            $nextPageNumber &&
            $nextPageNumber <= $lastPageNumber
        ) {
            $next = $uriBuilder
                ->reset()
                ->setAddQueryString('untrusted')
                ->uriFor(null, [$queryParam => $nextPageNumber]);
        }

        $pages = [];

        for ($page = $firstPageNumber; $page <= $lastPageNumber; $page++) {
            $link = $uriBuilder
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
}
