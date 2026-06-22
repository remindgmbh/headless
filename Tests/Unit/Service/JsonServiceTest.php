<?php

declare(strict_types=1);

namespace Remind\Headless\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Remind\Headless\Service\JsonService;
use TYPO3\CMS\Core\Pagination\PaginationInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(JsonService::class)]
class JsonServiceTest extends UnitTestCase
{
    #[Test]
    public function testSerializePagination(): void
    {
        $uriBuilder = $this->createMock(UriBuilder::class);
        $uriBuilder
            ->expects(self::exactly(7))
            ->method('reset')
            ->willReturnSelf();
        $uriBuilder
            ->expects(self::exactly(7))
            ->method('setAddQueryString')
            ->with('untrusted')
            ->willReturnSelf();
        $uriBuilder
            ->expects(self::exactly(7))
            ->method('uriFor')
            ->willReturnCallback(static function (?string $actionName, array $arguments): string {
                return '/?page=' . $arguments['page'] . ($actionName === null ? '' : '');
            });

        $pagination = $this->createMock(PaginationInterface::class);
        $pagination
            ->method('getFirstPageNumber')
            ->willReturn(1);
        $pagination
            ->method('getLastPageNumber')
            ->willReturn(3);
        $pagination
            ->method('getPreviousPageNumber')
            ->willReturn(1);
        $pagination
            ->method('getNextPageNumber')
            ->willReturn(3);
        $pagination
            ->method('getStartRecordNumber')
            ->willReturn(11);
        $pagination
            ->method('getEndRecordNumber')
            ->willReturn(20);

        $result = (new JsonService())->serializePagination($uriBuilder, $pagination, 'page', 2);

        self::assertSame([
            'endRecordNumber' => 20,
            'first' => '/?page=1',
            'last' => '/?page=3',
            'next' => '/?page=3',
            'pages' => [
                [
                    'active' => false,
                    'link' => '/?page=1',
                    'pageNumber' => 1,
                ],
                [
                    'active' => true,
                    'link' => '/?page=2',
                    'pageNumber' => 2,
                ],
                [
                    'active' => false,
                    'link' => '/?page=3',
                    'pageNumber' => 3,
                ],
            ],
            'prev' => '/?page=1',
            'startRecordNumber' => 11,
        ], $result);
    }
}
