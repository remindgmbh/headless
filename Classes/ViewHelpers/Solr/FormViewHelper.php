<?php

declare(strict_types = 1);

namespace Remind\Typo3Headless\ViewHelpers\Solr;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use ApacheSolrForTypo3\Solr\Util;

class FormViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;
    const ARGUMENT_PLUGIN_NAMESPACE = 'pluginNamespace';
    const ARGUMENT_SETTINGS = 'settings';
    public function initializeArguments()
    {
        $this->registerArgument(self::ARGUMENT_PLUGIN_NAMESPACE, 'string', 'plugin namespace', true);
        $this->registerArgument(self::ARGUMENT_SETTINGS, 'array', 'settings', true);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
        )
    {
        $pluginNamespace = $arguments[self::ARGUMENT_PLUGIN_NAMESPACE];
        $settings = $arguments[self::ARGUMENT_SETTINGS];

        $config = Util::getSolrConfiguration();
        $targetPageUid = $config->getSearchTargetPage();

        $uriBuilder = $renderingContext->getUriBuilder();

        $searchUrl = $uriBuilder
            ->reset()
            ->setTargetPageUid($targetPageUid)
            ->build();

        $suggestUrl = $uriBuilder
            ->reset()
            ->setTargetPageUid($targetPageUid)
            ->setTargetPageType((int)$settings['suggest']['typeNum'])
            ->build();

        $result = [
            'search' => [
                'url' => $searchUrl,
                'queryParam' => $pluginNamespace . '[q]',
            ],
            'suggest' => [
                'url' => $suggestUrl,
                'queryParam' => $pluginNamespace . '[queryString]',
            ],
        ];

        return $result;
    }
}