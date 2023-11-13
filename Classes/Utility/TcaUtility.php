<?php

declare(strict_types=1);

namespace Remind\Headless\Utility;

class TcaUtility
{
    /**
     * @param array $variants array with breakpoint names as key and aspect-ratios as value
     * the value can contain multple aspect ratios consisting of value and title
     * e.g. [
     *          'lg' => [
     *              ['value' => 8 / 5, 'title' => '8:5 (default)'],
     *              ['value' => 2 / 1, 'title' => '2:1 (alternative layout)'],
     *          ],
     *          'xxl' => [
     *              ['value' => 16 / 9, 'title' => '16:9']
     *          ]
     *      ]
     */
    public static function getCropVariants(array $variants): array
    {
        return array_reduce(array_keys($variants), function (array $result, string $breakpoint) use ($variants) {
            $aspectRatios = $variants[$breakpoint];
            $result[$breakpoint] = [
                'title' => $breakpoint,
                'allowedAspectRatios' => array_reduce(array_keys($aspectRatios), function (
                    array $result,
                    string $key
                ) use (
                    $aspectRatios,
                    $breakpoint,
                ) {
                    $aspectRatio = $aspectRatios[$key];
                    $result[$breakpoint . '_' . $aspectRatio['value']] = $aspectRatio;
                    return $result;
                }, []),
            ];
            return $result;
        }, []);
    }
}
