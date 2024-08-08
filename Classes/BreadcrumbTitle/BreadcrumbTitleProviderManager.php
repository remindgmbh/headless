<?php

declare(strict_types=1);

namespace Remind\Headless\BreadcrumbTitle;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Service\DependencyOrderingService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use UnexpectedValueException;

class BreadcrumbTitleProviderManager implements SingletonInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var string[]
     */
    private array $breadcrumbTitleCache = [];

    public function getTitle(): string
    {
        $breadcrumbTitle = '';

        $titleProviders = $this->getBreadcrumbTitleProviderConfiguration();
        $titleProviders = $this->setProviderOrder($titleProviders);

        $orderedTitleProviders = GeneralUtility::makeInstance(DependencyOrderingService::class)
            ->orderByDependencies($titleProviders);

        $this->logger?->debug('Breadcrumb title providers ordered', [
            'orderedTitleProviders' => $orderedTitleProviders,
        ]);

        foreach ($orderedTitleProviders as $provider => $configuration) {
            if (
                class_exists($configuration['provider']) &&
                is_subclass_of($configuration['provider'], BreadcrumbTitleProviderInterface::class)
            ) {
                /** @var BreadcrumbTitleProviderInterface $titleProviderObject */
                $titleProviderObject = GeneralUtility::makeInstance($configuration['provider']);
                if (
                    ($breadcrumbTitle = $titleProviderObject->getTitle())
                    || ($breadcrumbTitle = $this->breadcrumbTitleCache[$configuration['provider']] ?? '') !== ''
                ) {
                    $this->logger?->debug('Breadcrumb title provider {provider} used on page {title}', [
                        'provider' => $configuration['provider'],
                        'title' => $breadcrumbTitle,
                    ]);
                    $this->breadcrumbTitleCache[$configuration['provider']] = $breadcrumbTitle;
                    break;
                }
                $this->logger?->debug('Breadcrumb title provider {provider} skipped on page {title}', [
                    'provider' => $configuration['provider'],
                    'providerUsed' => $configuration['provider'],
                    'title' => $breadcrumbTitle,
                ]);
            }
        }

        return $breadcrumbTitle;
    }

    /**
     * @param mixed[] $orderInformation
     * @return mixed[]
     */
    protected function setProviderOrder(array $orderInformation): array
    {
        foreach ($orderInformation as $provider => &$configuration) {
            if (isset($configuration['before'])) {
                if (is_string($configuration['before'])) {
                    $configuration['before'] = GeneralUtility::trimExplode(',', $configuration['before'], true);
                } elseif (!is_array($configuration['before'])) {
                    throw new UnexpectedValueException(
                        'The specified "before" order configuration for provider "' . $provider . '" is invalid.',
                        1535803185
                    );
                }
            }
            if (isset($configuration['after'])) {
                if (is_string($configuration['after'])) {
                    $configuration['after'] = GeneralUtility::trimExplode(',', $configuration['after'], true);
                } elseif (!is_array($configuration['after'])) {
                    throw new UnexpectedValueException(
                        'The specified "after" order configuration for provider "' . $provider . '" is invalid.',
                        1535803186
                    );
                }
            }
        }
        return $orderInformation;
    }

    /**
     * @return mixed[]
     */
    private function getBreadcrumbTitleProviderConfiguration(): array
    {
        $typoscriptService = GeneralUtility::makeInstance(TypoScriptService::class);
        $config = $typoscriptService->convertTypoScriptArrayToPlainArray(
            $GLOBALS['TSFE']->config['config'] ?? []
        );

        return $config['breadcrumbTitleProviders'] ?? [];
    }
}
