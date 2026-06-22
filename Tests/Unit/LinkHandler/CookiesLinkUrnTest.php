<?php

declare(strict_types=1);

namespace Remind\Headless\Tests\Unit\LinkHandler;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Remind\Headless\LinkHandler\CookiesLinkBuilder;
use Remind\Headless\LinkHandler\CookiesLinkHandling;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(CookiesLinkHandling::class)]
#[CoversClass(CookiesLinkBuilder::class)]
class CookiesLinkUrnTest extends UnitTestCase
{
    #[Test]
    public function handlingBuildsAndResolvesCookiesUrn(): void
    {
        $handling = new CookiesLinkHandling();

        self::assertSame('t3://cookies?action=accept', $handling->asString(['action' => 'accept']));
        self::assertSame(['action' => 'decline'], $handling->resolveHandlerData(['action' => 'decline']));
        self::assertSame(['action' => ''], $handling->resolveHandlerData([]));
    }

    #[Test]
    public function builderCreatesCookiesUrnLinkResult(): void
    {
        $builder = new CookiesLinkBuilder($this->createMock(ContentObjectRenderer::class));
        $linkDetails = ['action' => 'settings'];

        $result = $builder->build($linkDetails, 'Cookie settings', '_blank', ['foo' => 'bar']);

        self::assertSame('cookies', $result->getType());
        self::assertSame('t3://cookies?action=settings', $result->getUrl());
        self::assertSame('_blank', $result->getTarget());
        self::assertSame('Cookie settings', $result->getLinkText());
        self::assertSame(['foo' => 'bar'], $result->getLinkConfiguration());
    }
}
