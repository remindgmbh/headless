<?php

declare(strict_types=1);

namespace Remind\Headless\Utility;

class TcaUtility
{
    /**
     * @param array $variants array with breakpoint names as key and aspect-ratio as array value containing value and title, e.g. ['lg' => [8 / 5, '8:5'], 'xxl' => [16 / 9, '16:9']]
     */
    public static function getCropVariants(array $variants): array
    {
        return array_reduce(array_keys($variants), function (array $result, string $breakpoint) use ($variants) {
            [$value, $title] = $variants[$breakpoint];
            $result[$breakpoint] = [
                'title' => $breakpoint,
                'allowedAspectRatios' => [
                    $breakpoint => [
                        'value' => $value,
                        'title' => $title,
                    ],
                ],
            ];
            return $result;
        }, []);
    }
}
