<?php

declare(strict_types=1);

namespace Remind\Typo3Headless\ViewHelpers\Solr;

use ApacheSolrForTypo3\Solr\ViewHelpers\Document\HighlightResultViewHelper;
use Remind\Typo3Headless\ViewHelpers\PaginationViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class ResultsViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    private const ARGUMENT_RESULT_SET = 'resultSet';
    private const ARGUMENT_PAGINATION = 'pagination';
    private const ARGUMENT_CURRENT_PAGE = 'currentPage';

    public function initializeArguments()
    {
        $this->registerArgument(self::ARGUMENT_RESULT_SET, 'object', 'results', true);
        $this->registerArgument(self::ARGUMENT_PAGINATION, 'object', 'pagination', true);
        $this->registerArgument(self::ARGUMENT_CURRENT_PAGE, 'int', 'current page', true);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        /** @var \ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResultSet $resultSet */
        $resultSet = $arguments[self::ARGUMENT_RESULT_SET];
        /** @var \ApacheSolrForTypo3\Solr\Pagination\ResultsPagination $pagination */
        $pagination = $arguments[self::ARGUMENT_PAGINATION];

        $currentPage = $arguments[self::ARGUMENT_CURRENT_PAGE];

        $viewHelperInvoker = $renderingContext->getViewHelperInvoker();

        $documents = [];

        $searchResults = $resultSet->getSearchResults();

        foreach ($searchResults as $searchResult) {
            /** @var \ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\Result\SearchResult $searchResult */
            $documents[] = [
                'title' => $searchResult->getTitle(),
                'content' => $viewHelperInvoker->invoke(
                    HighlightResultViewHelper::class,
                    ['resultSet' => $resultSet, 'document' => $searchResult, 'fieldName' => 'content'],
                    $renderingContext
                ),
                'url' => $searchResult->getUrl(),
            ];
        }

        $paginationResult = $viewHelperInvoker->invoke(
            PaginationViewHelper::class,
            ['pagination' => $pagination, 'currentPage' => $currentPage, 'queryParam' => 'page'],
            $renderingContext,
        );

        $count = $resultSet->getAllResultCount();

        $usedQuery = $resultSet->getUsedQuery();
        $query = $usedQuery ? $usedQuery->getOption('query') : null;

        return [
            'documents' => $documents,
            'count' => $count,
            'query' => $query,
            'pagination' => $paginationResult,
        ];
    }
}
