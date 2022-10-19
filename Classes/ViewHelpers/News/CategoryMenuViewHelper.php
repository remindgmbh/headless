<?php

declare(strict_types=1);

namespace Remind\Typo3Headless\ViewHelpers\News;

use Closure;
use RuntimeException;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class CategoryMenuViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    private const ARGUMENT_CATEGORIES = 'categories';
    private const ARGUMENT_SETTINGS = 'settings';
    private const ARGUMENT_OVERWRITE_DEMAND = 'overwriteDemand';

    public function initializeArguments()
    {
        $this->registerArgument(self::ARGUMENT_CATEGORIES, 'array', 'categories', true);
        $this->registerArgument(self::ARGUMENT_SETTINGS, 'array', 'settings', true);
        $this->registerArgument(self::ARGUMENT_OVERWRITE_DEMAND, 'array', 'overwriteDemand', true);
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
                1663759200
            );
        }

        $categories = $arguments[self::ARGUMENT_CATEGORIES];
        $settings = $arguments[self::ARGUMENT_SETTINGS];
        $overwriteDemand = $arguments[self::ARGUMENT_OVERWRITE_DEMAND];

        $overwriteDemandCategories = $overwriteDemand ? (int)($overwriteDemand['categories'] ?? false) : false;

        $uriBuilder = $renderingContext->getUriBuilder();

        $result = [
            'settings' => [
                'orderBy' => $settings['orderBy'],
                'orderDirection' => $settings['orderDirection'],
                'templateLayout' => $settings['templateLayout'],
                'action' => 'categoryMenu',
            ],
        ];

        $uri = $uriBuilder
            ->reset()
            ->setTargetPageUid((int)$settings['listPid'])
            ->uriFor();

        $result['categories'][] = [
            'title' => LocalizationUtility::translate('news.categoryMenu.all', 'rmnd_headless'),
            'slug' => $uri,
            'active' => !$overwriteDemandCategories,
        ];

        foreach ($categories as $category) {

            /** @var \GeorgRinger\News\Domain\Model\Category $item */
            $item = $category['item'];

            $uri = $uriBuilder
                ->reset()
                ->setTargetPageUid((int)$settings['listPid'])
                ->uriFor(null, ['overwriteDemand' => ['categories' => $item->getUid()]]);

            $result['categories'][] = [
                'uid' => $item->getUid(),
                'pid' => $item->getPid(),
                'title' => $item->getTitle(),
                'slug' => $uri,
                'active' => $overwriteDemandCategories === $item->getUid(),
                'seo' => [
                    'title' => $item->getSeoTitle(),
                    'description' => $item->getSeoDescription(),
                    'headline' => $item->getSeoHeadline(),
                    'text' => $item->getSeoText(),
                ],
            ];
        }

        return $result;
    }
}
