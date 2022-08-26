<?php

declare(strict_types = 1);

namespace Remind\Typo3Headless\ViewHelpers\News;

use GeorgRinger\News\Domain\Model\Category;
use GeorgRinger\News\ViewHelpers\Category\CountViewHelper;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class CategoryMenuViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    const ARGUMENT_CATEGORIES = 'categories';
    const ARGUMENT_SETTINGS = 'settings';
    const ARGUMENT_OVERWRITE_DEMAND = 'overwriteDemand';

    public function initializeArguments()
    {
        $this->registerArgument(self::ARGUMENT_CATEGORIES, 'array', 'categories', true);
        $this->registerArgument(self::ARGUMENT_SETTINGS, 'array', 'settings', true);
        $this->registerArgument(self::ARGUMENT_OVERWRITE_DEMAND, 'array', 'overwriteDemand', true);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
        )
    {
        $categories = $arguments[self::ARGUMENT_CATEGORIES];
        $settings = $arguments[self::ARGUMENT_SETTINGS];
        $overwriteDemand = $arguments[self::ARGUMENT_OVERWRITE_DEMAND];

        $overwriteDemandCategories = $overwriteDemand ? (int)($overwriteDemand['categories'] ?? false) : false;

        $viewHelperInvoker = $renderingContext->getViewHelperInvoker();
        $uriBuilder = $renderingContext->getUriBuilder();

        $result = [
            'settings' => [
                'orderBy' => $settings['orderBy'],
                'orderDirection' => $settings['orderDirection'],
                'templateLayout' => $settings['templateLayout'],
                'action' => 'categoryMenu'
            ]
        ];

        $uri = $uriBuilder
            ->reset()
            ->setTargetPageUid((int)$settings['listPid'])
            ->build();

        $allCategories = [
            'title' => LocalizationUtility::translate('news.categoryMenu.all', 'rmnd_headless'),
            'slug' => $uri,
            'active' => !$overwriteDemandCategories,
            'count' => 0,
        ];

        $result['categories'][] = &$allCategories;

        foreach ($categories as $category) {

            /** @var Category $item */
            $item = $category['item'];

            /** @var Category|null $parent */
            $parent = $category['parent'];

            $uri = $uriBuilder
                ->reset()
                ->setTargetPageUid((int)$settings['listPid'])
                ->setArguments([
                    'tx_news_pi1' => [
                        'overwriteDemand' => [
                            'categories' => $item->getUid()
                        ]
                    ]
                ])
                ->build();

            $count = $viewHelperInvoker->invoke(
                CountViewHelper::class,
                ['categoryUid' => $item->getUid()],
                $renderingContext)
            ;

            $allCategories['count'] += $count;
            
            $result['categories'][] = [
                'uid' => $item->getUid(),
                'pid' => $item->getPid(),
                'title' => $item->getTitle(),
                'slug' => $uri,
                'active' => $overwriteDemandCategories === $item->getUid(),
                'count' => $count,
                'seo' => [
                    'title' => $item->getSeoTitle(),
                    'description' => $item->getSeoDescription(),
                    'headline' => $item->getSeoHeadline(),
                    'text' => $item->getSeoText()
                ]
            ];
        }

        return $result;
    }
}