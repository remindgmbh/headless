<?php

declare(strict_types=1);

namespace Remind\Headless\Tests\Unit\Utility;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Remind\Headless\Utility\TcaUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(TcaUtility::class)]
class TcaUtilityTest extends UnitTestCase
{
    #[Test]
    public function getCropVariantsBuildsBreakpointVariantStructure(): void
    {
        $result = TcaUtility::getCropVariants([
            'lg' => [
                [
                    'title' => '8:5 (default)',
                    'value' => 1.6,
                ],
                [
                    'title' => '2:1 (alternative layout)',
                    'value' => 2.0,
                ],
            ],
            'xxl' => [
                [
                    'title' => '16:9',
                    'value' => 1.7777777778,
                ],
            ],
        ]);

        self::assertSame('lg', $result['lg']['title']);
        self::assertSame('xxl', $result['xxl']['title']);

        self::assertSame(
            [
                'lg_1.6' => [
                    'title' => '8:5 (default)',
                    'value' => 1.6,
                ],
                'lg_2' => [
                    'title' => '2:1 (alternative layout)',
                    'value' => 2.0,
                ],
            ],
            $result['lg']['allowedAspectRatios']
        );

        self::assertSame(
            [
                'xxl_1.7777777778' => [
                    'title' => '16:9',
                    'value' => 1.7777777778,
                ],
            ],
            $result['xxl']['allowedAspectRatios']
        );
    }

    #[Test]
    public function getCropVariantsFreeCreatesFreeVariantForEachBreakpoint(): void
    {
        $freeRatioTitle = 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.free';
        $result = TcaUtility::getCropVariantsFree(['mobile', 'desktop']);

        self::assertSame(['mobile', 'desktop'], array_keys($result));

        self::assertSame('mobile', $result['mobile']['title']);
        self::assertSame(
            [
                'mobile_0' => [
                    'title' => $freeRatioTitle,
                    'value' => 0.0,
                ],
            ],
            $result['mobile']['allowedAspectRatios']
        );

        self::assertSame('desktop', $result['desktop']['title']);
        self::assertSame(
            [
                'desktop_0' => [
                    'title' => $freeRatioTitle,
                    'value' => 0.0,
                ],
            ],
            $result['desktop']['allowedAspectRatios']
        );
    }
}
