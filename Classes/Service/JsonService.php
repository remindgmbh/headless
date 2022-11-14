<?php

declare(strict_types=1);

namespace Remind\Headless\Service;

use JsonSerializable;
use Psr\Log\LoggerInterface;
use Remind\Extbase\FlexForms\ListSheets;
use Remind\Extbase\Service\Dto\FilterableListResult;
use Remind\Extbase\Service\Dto\FrontendFilter;
use Remind\Extbase\Service\Dto\ListResult;
use TYPO3\CMS\Core\Pagination\PaginationInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
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

    public function serializeList(
        ListResult $listResult,
        int $page,
        string $detailActionName,
        string $detailUidArgument,
    ): array {
        $paginationJson = null;
        $pagination = $listResult->getPagination();
        if ($pagination) {
            $paginationJson = $this->serializePagination(
                $pagination,
                'page',
                $page,
            );
        }

        $items = $listResult->getQueryResult()->toArray();
        $itemsJson = $this->serializeListItems($items, $detailActionName, $detailUidArgument);

        return [
            'count' => $listResult->getCount(),
            'items' => $itemsJson,
            'pagination' => $paginationJson,
        ];
    }

    public function serializeFilterableList(
        FilterableListResult $listResult,
        int $page,
        string $detailActionName,
        string $detailUidArgument,
        string $filterArgument
    ): array {
        $result = $this->serializeList($listResult, $page, $detailActionName, $detailUidArgument);
        $filters = $listResult->getFrontendFilters();
        $filtersJson = $this->serializeFilters($filters, $filterArgument);
        $result['filters'] = $filtersJson;
        return $result;
    }

    /**
     * @param FrontendFilter[] $filters
     * @return array
     */
    private function serializeFilters(array $filters, string $filterArguments): array
    {
        $result = [];
        $activeFilterValues = array_reduce($filters, function (array $result, FrontendFilter $filterData) {
            $values = $filterData->getActiveArgumentValues();
            if (!empty($values)) {
                $result[$filterData->getFieldName()] = implode(',', $values);
            }
            return $result;
        }, []);
        foreach ($filters as $filter) {
            $fieldName = $filter->getFieldName();
            $filterJson = [
                'name' => $fieldName,
                'label' => $filter->getLabel(),
                'values' => [],
            ];

            foreach ($filter->getValues() as $filterValue) {
                $args = [];
                // copy $activeFilterValues to $args array
                ArrayUtility::mergeRecursiveWithOverrule($args, $activeFilterValues);

                if (($args[$fieldName] ?? null) === $filterValue->getArgumentValue()) {
                    // remove argument if it is already active so the link removes the filter
                    unset($args[$fieldName]);
                } else {
                    $args[$fieldName] = $filterValue->getArgumentValue();
                }

                $url = $this->uriBuilder
                    ->reset()
                    ->uriFor(null, [$filterArguments => $args]);

                $filterValueJson = [
                    'value' => $filterValue->getValue(),
                    'link' => $url,
                    'disabled' => $filterValue->isDisabled(),
                    'active' => $filterValue->isActive(),
                ];

                $filterJson['values'][] = $filterValueJson;
            }

            $result[] = $filterJson;
        }
        return $result;
    }

    private function serializeListItems(array $items, string $detailActionName, string $detailUidArgument): array
    {
        return array_map(function (AbstractEntity $item) use ($detailActionName, $detailUidArgument) {
            if (!($item instanceof JsonSerializable)) {
                $this->logger->warning(
                    'Class "{class}" does not implement "{interface}" Interface.',
                    [
                        'class' => get_class($item),
                        'interface' => JsonSerializable::class,
                    ]
                );
            }
            $itemJson = json_decode(json_encode($item), true);
            $link = $this->uriBuilder
                ->reset()
                ->setTargetPageUid((int) ($this->settings[ListSheets::DETAIL_PAGE] ?? null))
                ->uriFor($detailActionName, [$detailUidArgument => $item->getUid()]);
            $itemJson['link'] = $link;
            return $itemJson;
        }, $items);
    }
}