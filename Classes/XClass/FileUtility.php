<?php

declare(strict_types=1);

namespace Remind\Headless\XClass;

use FriendsOfTYPO3\Headless\Utility\FileUtility as BaseFileUtility;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use Remind\Headless\Event\ModifyFilePropertiesEvent;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\Rendering\RendererRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

// TODO: remove once implemended in EXT:headless
class FileUtility extends BaseFileUtility
{
    private ?EventDispatcherInterface $eventDispatcher = null;
    
    public function __construct(
        ?ContentObjectRenderer $contentObjectRenderer = null,
        ?RendererRegistry $rendererRegistry = null,
        ?ImageService $imageService = null,
        ?ServerRequestInterface $serverRequest = null,
        ?EventDispatcherInterface $eventDispatcher = null,
    ) {
        parent::__construct($contentObjectRenderer, $rendererRegistry, $imageService, $serverRequest);
        $this->eventDispatcher = $eventDispatcher ?? GeneralUtility::makeInstance(EventDispatcher::class);
    }

        /**
     * @param array $dimensions
     * @return array
     */
    public function processFile(
        FileInterface $fileReference,
        array $dimensions = [],
        string $cropVariant = 'default',
        bool $delayProcessing = false
    ): array {
        $result = parent::processFile($fileReference, $dimensions, $cropVariant, $delayProcessing);
        $metaData = $fileReference->toArray();
        /** @var ModifyFilePropertiesEvent $event */
        $event = $this->eventDispatcher->dispatch(new ModifyFilePropertiesEvent($result['properties'], $metaData));
        $result['properties'] = $event->getProperties();
        return $result;
    }
}
