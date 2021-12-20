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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
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
     */
    public function resultsAction()
    {
        try {
            $arguments = (array)$this->request->getArguments();
            $pageId = $this->typoScriptFrontendController->getRequestedId();
            $languageId = Util::getLanguageUid();
            $searchRequest = $this->getSearchRequestBuilder()->buildForSearch($arguments, $pageId, $languageId);

            $searchResultSet = $this->searchService->search($searchRequest);

            $searchResults = $searchResultSet->getSearchResults();

            $mapSearchResult = fn (SearchResult $searchResult) => [
                'title' => $this->getHighlightedContent($searchResultSet, $searchResult, 'title'),
                'content' => $this->getHighlightedContent($searchResultSet, $searchResult, 'content'),
                'url' => json_decode($searchResult->getUrl(), true)
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

            return json_encode(array_merge(['result' => $result], json_decode($this->formAction(), true)));
        } catch (SolrUnavailableException $e) {
            $this->handleSolrUnavailable();
        }
    }

    public function solrNotAvailableAction()
    {
        // return response code 200 with error message to be handled in frontend
        return LocalizationUtility::translate(
            'LLL:EXT:solr/Resources/Private/Language/locallang.xlf:searchUnavailable'
        );
    }

    /**
     * Form
     */
    public function formAction()
    {
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

        return json_encode(['form' => $form]);
    }

    protected function getHighlightedContent(
        SearchResultSet $searchResultSet,
        SearchResult $searchResult,
        string $fieldName
    ) {
        return $this->viewHelperInvoker->invoke(
            HighlightResultViewHelper::class,
            ['resultSet' => $searchResultSet, 'document' => $searchResult, 'fieldName' => $fieldName],
            new RenderingContext(),
        );
    }

    protected function getSuggestUrl(int $targetPageUid): string
    {
        $typeNum = (int)$this->typoScriptConfiguration->getValueByPath('plugin.tx_solr.suggest.typeNum');
        $suggestUrl = $this->uriBuilder
            ->reset()
            ->setTargetPageUid($targetPageUid)
            ->setTargetPageType($typeNum)
            ->setUseCacheHash(false)
            ->build();

        /** @var URLHelper $urlService */
        $urlService = GeneralUtility::makeInstance(UrlHelper::class, $suggestUrl);
        $suggestUrl = $urlService->withoutQueryParameter('cHash')->__toString();

        return $suggestUrl;
    }
}
