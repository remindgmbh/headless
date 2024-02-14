<?php

declare(strict_types=1);

namespace Remind\Headless\Service;

use FriendsOfTYPO3\Headless\Utility\FileUtility;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Pagination\PaginationInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Web\RequestBuilder;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Service\ImageService;

class JsonService
{
    private array $settings = [];

    public function __construct(
        private readonly UriBuilder $uriBuilder,
        private readonly LoggerInterface $logger,
        private readonly ImageService $imageService,
        private readonly FileUtility $fileUtility,
        RequestBuilder $requestBuilder,
        ConfigurationManagerInterface $configurationManager
    ) {
        $extbaseRequest = $requestBuilder->build($this->getRequest());
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
            ->setAddQueryString('untrusted')
            ->uriFor(null, [$queryParam => $firstPageNumber]);

        $last = $this->uriBuilder
            ->reset()
            ->setAddQueryString('untrusted')
            ->uriFor(null, [$queryParam => $lastPageNumber]);

        if ($previousPageNumber && $previousPageNumber >= $firstPageNumber) {
            $prev = $this->uriBuilder
                ->reset()
                ->setAddQueryString('untrusted')
                ->uriFor(null, [$queryParam => $previousPageNumber]);
        }

        if ($nextPageNumber && $nextPageNumber <= $lastPageNumber) {
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
                'pageNumber' => $page,
                'link' => $link,
                'active' => $page === $currentPage,
            ];
        }

        $result = [
            'startRecordNumber' => $pagination->getStartRecordNumber(),
            'endRecordNumber' => $pagination->getEndRecordNumber(),
            'first' => $first,
            'last' => $last,
            'prev' => $prev ?? null,
            'next' => $next ?? null,
            'pages' => $pages,
        ];

        return $result;
    }

    public function processImage(int $uid): ?array
    {
        $imageObj = $this->imageService->getImage(strval($uid), null, true);
        return $this->fileUtility->processFile($imageObj);
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
