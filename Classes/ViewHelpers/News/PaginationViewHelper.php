<?php

declare(strict_types = 1);

namespace Remind\Typo3Headless\ViewHelpers\News;

use GeorgRinger\News\Pagination\QueryResultPaginator;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class PaginationViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    const ARGUMENT_PAGINATION = 'pagination';
    const ARGUMENT_PAGINATOR = 'paginator';
    public function initializeArguments()
    {
        $this->registerArgument(self::ARGUMENT_PAGINATION, 'object', 'pagination', true);
        $this->registerArgument(self::ARGUMENT_PAGINATOR, 'object', 'paginator', true);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
        )
    {
        /** @var SimplePagination $pagination */
        $pagination = $arguments[self::ARGUMENT_PAGINATION];

        /** @var QueryResultPaginator $paginator */
        $paginator = $arguments[self::ARGUMENT_PAGINATOR];

        $uriBuilder = $renderingContext->getUriBuilder();

        $firstPageNumber = $pagination->getFirstPageNumber();
        $lastPageNumber = $pagination->getLastPageNumber();
        $previousPageNumber = $pagination->getPreviousPageNumber();
        $nextPageNumber = $pagination->getNextPageNumber();

        $first = $uriBuilder
            ->reset()
            ->setAddQueryString(true)
            ->setArgumentsToBeExcludedFromQueryString(['tx_news_pi1[currentPage]'])
            ->uriFor();

        $last = $uriBuilder
            ->reset()
            ->setAddQueryString(true)
            ->uriFor(null, ['currentPage' => $lastPageNumber]);

        if ($previousPageNumber && $previousPageNumber >= $firstPageNumber) {
            if ($previousPageNumber === $firstPageNumber) {
                $prev = $first;
            } else {
                $prev = $uriBuilder
                    ->reset()
                    ->setAddQueryString(true)
                    ->uriFor(null, ['currentPage' => $previousPageNumber]);
            }
        }

        if ($nextPageNumber && $nextPageNumber <= $lastPageNumber) {
            $next = $uriBuilder
                ->reset()
                ->setAddQueryString(true)
                ->uriFor(null, ['currentPage' => $nextPageNumber]);
        }

        $pages = [];

        foreach ($pagination->getAllPageNumbers() as $page) {
            if ($page === $firstPageNumber) {
                $link = $first;
            } else {
                $link = $uriBuilder
                    ->reset()
                    ->setAddQueryString(true)
                    ->uriFor(null, ['currentPage' => $page]);
            }
            
            $pages[] = [
                'page' => $page,
                'link' => $link,
                'current' => $page === $paginator->getCurrentPageNumber()
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