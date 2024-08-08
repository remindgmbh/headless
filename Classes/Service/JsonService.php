<?php

declare(strict_types=1);

namespace Remind\Headless\Service;

use FriendsOfTYPO3\Headless\Utility\FileUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Pagination\PaginationInterface;
use TYPO3\CMS\Extbase\Mvc\Web\RequestBuilder;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Service\ImageService;

class JsonService
{
    public function __construct(
        private readonly UriBuilder $uriBuilder,
        private readonly ImageService $imageService,
        private readonly FileUtility $fileUtility,
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

    /**
     * @return mixed[]
     */
    public function processImage(int $uid): array
    {
        $imageObj = $this->imageService->getImage(strval($uid), null, true);
        return $this->fileUtility->processFile($imageObj);
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
