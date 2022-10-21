<?php

declare(strict_types=1);

namespace Remind\Headless\ViewHelpers;

use Closure;
use RuntimeException;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class PaginationViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    public const ARGUMENT_PAGINATION = 'pagination';
    public const ARGUMENT_CURRENT_PAGE = 'currentPage';
    public const ARGUMENT_QUERY_PARAM = 'queryParam';

    public function initializeArguments()
    {
        $this->registerArgument(self::ARGUMENT_PAGINATION, 'object', 'pagination', true);
        $this->registerArgument(self::ARGUMENT_CURRENT_PAGE, 'int', 'current page', true);
        $this->registerArgument(self::ARGUMENT_QUERY_PARAM, 'string', 'query param', true);
    }

    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        if (!$renderingContext instanceof RenderingContext) {
            throw new RuntimeException(
                sprintf(
                    'RenderingContext must be instance of "%s", but is instance of "%s"',
                    RenderingContext::class,
                    get_class($renderingContext)
                ),
                1663759103
            );
        }

        /** @var \TYPO3\CMS\Core\Pagination\PaginationInterface $pagination */
        $pagination = $arguments[self::ARGUMENT_PAGINATION];
        /** @var int $currentPage */
        $currentPage = $arguments[self::ARGUMENT_CURRENT_PAGE];
        $queryParam = $arguments[self::ARGUMENT_QUERY_PARAM];

        $uriBuilder = $renderingContext->getUriBuilder();

        $firstPageNumber = $pagination->getFirstPageNumber();
        $lastPageNumber = $pagination->getLastPageNumber();
        $previousPageNumber = $pagination->getPreviousPageNumber();
        $nextPageNumber = $pagination->getNextPageNumber();

        $first = $uriBuilder
            ->reset()
            ->setAddQueryString(true)
            ->uriFor(null, [$queryParam => $firstPageNumber]);

        $last = $uriBuilder
            ->reset()
            ->setAddQueryString(true)
            ->uriFor(null, [$queryParam => $lastPageNumber]);

        if ($previousPageNumber && $previousPageNumber >= $firstPageNumber) {
            $prev = $uriBuilder
                ->reset()
                ->setAddQueryString(true)
                ->uriFor(null, [$queryParam => $previousPageNumber]);
        }

        if ($nextPageNumber && $nextPageNumber <= $lastPageNumber) {
            $next = $uriBuilder
                ->reset()
                ->setAddQueryString(true)
                ->uriFor(null, [$queryParam => $nextPageNumber]);
        }

        $pages = [];

        for ($page = $firstPageNumber; $page <= $lastPageNumber; $page++) {
            $link = $uriBuilder
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
