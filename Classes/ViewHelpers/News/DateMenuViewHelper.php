<?php

declare(strict_types = 1);

namespace Remind\Typo3Headless\ViewHelpers\News;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class DateMenuViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    const ARGUMENT_DATA = 'data';
    const ARGUMENT_SETTINGS = 'settings';
    const ARGUMENT_OVERWRITE_DEMAND = 'overwriteDemand';

    public function initializeArguments()
    {
        $this->registerArgument(self::ARGUMENT_DATA, 'array', 'data', true);
        $this->registerArgument(self::ARGUMENT_SETTINGS, 'array', 'settings', true);
        $this->registerArgument(self::ARGUMENT_OVERWRITE_DEMAND, 'array', 'overwriteDemand', true);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
        )
    {
        $data = $arguments[self::ARGUMENT_DATA];
        $settings = $arguments[self::ARGUMENT_SETTINGS];
        $overwriteDemand = $arguments[self::ARGUMENT_OVERWRITE_DEMAND];

        $uriBuilder = $renderingContext->getUriBuilder();

        $result = [
            'settings' => [
                'orderBy' => $settings['orderBy'],
                'orderDirection' => $settings['orderDirection'],
                'templateLayout' => $settings['templateLayout'],
                'action' => 'dateMenu'
            ]
        ];

        $years = [];

        $uri = $uriBuilder
            ->reset()
            ->setTargetPageUid((int)$settings['listPid'])
            ->build();

        $allYears = [
            'title' => LocalizationUtility::translate('news.dateMenu.all', 'rmnd_headless'),
            'slug' => $uri,
            'active' => !$overwriteDemand['year'],
            'count' => 0
        ];

        $years[] = &$allYears;

        foreach ($data['single'] as $yearTitle => $months) {
            $yearUri = $uriBuilder
                ->reset()
                ->setTargetPageUid((int)$settings['listPid'])
                ->setArguments([
                    'tx_news_pi1' => [
                        'overwriteDemand' => [
                            'year' => $yearTitle
                        ]
                    ]
                ])
                ->build();

            $count = $data['total'][$yearTitle];

            $allYears['count'] += $count;

            $year = [
                'title' => $yearTitle,
                'slug' => $yearUri,
                'active' => (int)$overwriteDemand['year'] === $yearTitle && !$overwriteDemand['month'],
                'count' => $count,
                'months' => []
            ];

            foreach ($months as $month => $count) {
                $monthUri = $uriBuilder
                    ->reset()
                    ->setTargetPageUid((int)$settings['listPid'])
                    ->setArguments([
                        'tx_news_pi1' => [
                            'overwriteDemand' => [
                                'year' => $yearTitle,
                                'month' => $month
                            ]
                        ]
                    ])
                    ->build();

                $year['months'][] = [
                    'title' => $month,
                    'slug' => $monthUri,
                    'active' => (int)$overwriteDemand['year'] === $yearTitle && (int)$overwriteDemand['month'] === $month,
                    'count' => $count
                ];
            }
            
            $years[] = $year;
        }

        $result['years'] = $years;

        return $result;
    }
}