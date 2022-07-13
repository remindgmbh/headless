<?php

declare(strict_types=1);

namespace Remind\Typo3Headless\XClass\Controller;

use ApacheSolrForTypo3\Solr\Controller\SearchController;
use ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\Result\SearchResult;
use ApacheSolrForTypo3\Solr\Domain\Search\ResultSet\SearchResultSet;
use ApacheSolrForTypo3\Solr\System\Url\UrlHelper;
use ApacheSolrForTypo3\Solr\System\Solr\SolrUnavailableException;
use ApacheSolrForTypo3\Solr\ViewHelpers\Document\HighlightResultViewHelper;
use ApacheSolrForTypo3\Solr\Util;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException;
use TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInvoker;

class SolrSearchController extends SearchController
{
    /**
     * @var ViewHelperInvoker
     */
    protected $viewHelperInvoker;

    public function __construct()
    {
        $this->viewHelperInvoker = GeneralUtility::makeInstance(ViewHelperInvoker::class);
    }

    /**
     * Results
     * @return ResponseInterface
     * @throws AspectNotFoundException
     * @throws NoSuchArgumentException
     * @throws InvalidSlotException
     * @throws InvalidSlotReturnException
     */
    public function resultsAction(): ResponseInterface
    {
        try {
            $arguments = (array)$this->request->getArguments();
            $pageId = $this->typoScriptFrontendController->getRequestedId();
            $languageId = Util::getLanguageUid();
            $searchRequest = $this->getSearchRequestBuilder()->buildForSearch($arguments, $pageId, $languageId);

            $searchResultSet = $this->searchService->search($searchRequest);

            $searchResults = $searchResultSet->getSearchResults();

            $mapSearchResult = fn (SearchResult $searchResult) => [
                'title' => $searchResult->getTitle(),
                'content' => $this->getHighlightedContent($searchResultSet, $searchResult, 'content'),
                'url' => $searchResult->getUrl()
            ];

            $allResultCount = $searchResultSet->getAllResultCount();
            $usedResultsPerPage = $searchResultSet->getUsedResultsPerPage();
            $usedPage = $searchResultSet->getUsedPage();

            $itemsPerPage = (int)ceil($allResultCount / $usedResultsPerPage);
            $usedQuery = $searchResultSet->getUsedQuery();
            $query = $usedQuery ? $usedQuery->getOption('query') : null;

            $result = [
                'documents' => [
                    'pagination' => [
                        'current' => $usedPage == 0 && $itemsPerPage > 0  ? 1 : $usedPage,
                        'numberOfPages' => $itemsPerPage,
                    ],
                    'list' => array_map($mapSearchResult, $searchResults->getArrayCopy()),
                    'count' => $searchResults->getCount()
                ],
                'allResultCount' => $allResultCount,
                'query' => $query
            ];
            $form = $this->getForm();

            return $this->htmlResponse(json_encode(array_merge(['result' => $result], ['form' => $form])));
        } catch (SolrUnavailableException $e) {
            return $this->handleSolrUnavailable();
        }
    }

    /**
     * Form
     */
    public function formAction(): ResponseInterface
    {
        $form = $this->getForm();

        return $this->htmlResponse(json_encode(['form' => $form]));
    }

    protected function getForm(): array {
        $pluginNamespace = $this->typoScriptConfiguration->getSearchPluginNamespace();

        $targetPageUid = $this->typoScriptConfiguration->getSearchTargetPage();

        $queryParams = [
            'q' => $pluginNamespace . '[q]',
            'page' => $pluginNamespace . '[page]',
        ];

        $suggestUrl = $this->getSuggestUrl($targetPageUid);

        $form = [
            'targetUrl' => $this->uriBuilder->reset()->setTargetPageUid($targetPageUid)->build(),
            'pluginNamespace' => $pluginNamespace,
            'queryParams' => $queryParams,
            'suggest' => [
                'url' => $suggestUrl,
                'queryParam' => $pluginNamespace . '[queryString]',
            ],
        ];

        return $form;
    }

    protected function getHighlightedContent(
        SearchResultSet $searchResultSet,
        SearchResult $searchResult,
        string $fieldName
    ) {
        return $this->viewHelperInvoker->invoke(
            HighlightResultViewHelper::class,
            ['resultSet' => $searchResultSet, 'document' => $searchResult, 'fieldName' => $fieldName],
            $this->view->getRenderingContext()
        );
    }

    protected function getSuggestUrl(int $targetPageUid): string
    {
        $typeNum = (int)$this->typoScriptConfiguration->getValueByPath('plugin.tx_solr.suggest.typeNum');
        $suggestUrl = $this->uriBuilder
            ->reset()
            ->setTargetPageUid($targetPageUid)
            ->setTargetPageType($typeNum)
            ->build();

        /** @var URLHelper $urlService */
        $urlService = GeneralUtility::makeInstance(UrlHelper::class, $suggestUrl);
        $suggestUrl = $urlService->withoutQueryParameter('cHash')->__toString();

        return $suggestUrl;
    }
}
