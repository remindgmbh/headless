<?php

declare(strict_types=1);

namespace Remind\Headless\Tests\Unit\BreadcrumbTitle;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Remind\Headless\BreadcrumbTitle\BreadcrumbTitleProviderInterface;
use Remind\Headless\BreadcrumbTitle\BreadcrumbTitleProviderManager;
use TYPO3\CMS\Core\Service\DependencyOrderingService;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(BreadcrumbTitleProviderManager::class)]
class BreadcrumbTitleProviderManagerTest extends UnitTestCase
{
    #[Test]
    public function getTitleUsesProviderCacheFallbackWhenProviderReturnsEmptyTitle(): void
    {
        $request = $this->createRequestWithTypoScriptConfig();
        $provider = new class implements BreadcrumbTitleProviderInterface {
            public function setRequest(ServerRequestInterface $_request): void
            {
                unset($_request);
            }

            public function getTitle(): string
            {
                return '';
            }
        };
        $providerClass = get_class($provider);

        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects(self::once())
            ->method('get')
            ->with($providerClass)
            ->willReturn($provider);

        $dependencyOrderingService = $this->createMock(DependencyOrderingService::class);
        $dependencyOrderingService
            ->method('orderByDependencies')
            ->willReturn([
                [
                    'provider' => $providerClass,
                ],
            ]);

        $typoScriptService = $this->createMock(TypoScriptService::class);
        $typoScriptService
            ->method('convertTypoScriptArrayToPlainArray')
            ->with(['config' => 'available'])
            ->willReturn([
                'breadcrumbTitleProviders' => [
                    $providerClass => [
                        'provider' => $providerClass,
                    ],
                ],
            ]);

        $manager = new BreadcrumbTitleProviderManager($container, $dependencyOrderingService, $typoScriptService);
        $manager->setBreadcrumbTitleCache([
            $providerClass => 'Cached breadcrumb title',
        ]);

        self::assertSame('Cached breadcrumb title', $manager->getTitle($request));
    }

    private function createRequestWithTypoScriptConfig(): ServerRequestInterface
    {
        $frontendTypoScript = new class {
            /**
             * @return mixed[]
             */
            public function getConfigArray(): array
            {
                return ['config' => 'available'];
            }
        };

        $request = $this->createMock(ServerRequestInterface::class);
        $request
            ->method('getAttribute')
            ->with('frontend.typoscript')
            ->willReturn($frontendTypoScript);

        return $request;
    }
}
