<?php

declare(strict_types=1);

namespace Remind\Headless\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Imaging\ImageManipulation\Area;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileType;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Service\ImageService;

class AssetMiddleware implements MiddlewareInterface
{
    private ResponseFactoryInterface $responseFactory;

    private ImageService $imageService;

    private ResourceFactory $resourceFactory;

    public function injectImageService(ImageService $imageService): void
    {
        $this->imageService = $imageService;
    }

    public function injectResponseFactory(ResponseFactoryInterface $responseFactory): void
    {
        $this->responseFactory = $responseFactory;
    }

    public function injectResourceFactory(ResourceFactory $resourceFactory): void
    {
        $this->resourceFactory = $resourceFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var \TYPO3\CMS\Core\Routing\SiteRouteResult $routing */
        $routing = $request->getAttribute('routing');
        $path = $routing->getUri()->getPath();
        $queryParams = $request->getQueryParams();
        $uid = (int) ($queryParams['uid'] ?? null);
        $uidLocal = (int) ($queryParams['uidLocal'] ?? null);
        if (
            $path === '/asset' &&
            (
                $uid ||
                $uidLocal
            )
        ) {
            $resource = $uid
                ? $this->resourceFactory->getFileReferenceObject($uid)
                : $this->resourceFactory->getFileObject($uidLocal);

            $tstamp = intval($resource->getProperty('tstamp'));
            $lastModified = gmdate('D, d M Y H:i:s', $tstamp) . ' GMT';
            $ifModifiedSince = $request->getHeader('if-modified-since')[0] ?? null;

            if ($lastModified === $ifModifiedSince) {
                return $this->responseFactory->createResponse(304);
            }

            $type = (int) $resource->getProperty('type');

            $processedResource = $resource;

            if ($type === FileType::IMAGE->value) {
                $targetFileExtension = $queryParams['fileExtension'] ?? null;

                $cropName = $queryParams['crop'] ?? null;
                $breakpoint = $queryParams['breakpoint'] ?? null;

                $cropVariants = array_filter([
                    implode('-', array_filter([$cropName, $breakpoint])),
                    $cropName,
                    $breakpoint,
                ]);

                $crop = $resource->getProperty('crop') ?? '';
                $cropVariantCollection = CropVariantCollection::create($crop);

                $cropArea = Area::createEmpty();

                foreach ($cropVariants as $cropVariant) {
                    $cropArea = $cropVariantCollection->getCropArea($cropVariant);
                    if (!$cropArea->isEmpty()) {
                        break;
                    }
                }

                $skipProcessing = false;

                    // Use default cropVariant if breakpoint cropVariant does not exist
                if ($cropArea->isEmpty()) {
                    $cropArea = $cropVariantCollection->getCropArea();

                    /**
                     * Skip processing if cropArea is empty and
                     *  - targetFileExtension is SVG because TYPO3 cannot process SVGs
                     *  - source is SVG and targetFileExtension is not set
                     *    (normally TYPO3 would convert SVGs to PNGs with targetFileExtension not set,
                     *     but there is no need with an empty cropArea)
                     */
                    if (
                        $targetFileExtension === 'svg' ||
                        $resource->getExtension() === 'svg' &&
                        !$targetFileExtension
                    ) {
                        $skipProcessing = true;
                    }
                } elseif (
                    $resource->getExtension() === 'svg' &&
                    $targetFileExtension === 'svg'
                ) {
                    // TYPO3 converts SVGs to PNGs if targetFileExtension is not set
                    $targetFileExtension = null;
                }

                if (!$skipProcessing) {
                    $processingInstructions = [
                        'crop' => $cropArea->makeAbsoluteBasedOnFile($resource),
                        'fileExtension' => $targetFileExtension ?? null,
                        'height' => $queryParams['height'] ?? null,
                        'maxHeight' => $queryParams['maxHeight'] ?? null,
                        'maxWidth' => $queryParams['maxWidth'] ?? null,
                        'width' => $queryParams['width'] ?? null,
                    ];

                    $processedResource = $this->imageService->applyProcessingInstructions(
                        $resource,
                        $processingInstructions
                    );
                }
            }

            $mimeType = $processedResource->getMimeType();
            $contents = $processedResource->getContents();

            $title = $resource->getProperty('title') ?? $resource->getNameWithoutExtension();
            $filename = str_replace(' ', '-', strtolower($title)) . '.' . $processedResource->getExtension();

            $response = $this->responseFactory
                ->createResponse()
                ->withHeader('Content-Type', $mimeType)
                ->withHeader('Content-Disposition', 'inline;filename="' . $filename . '"')
                ->withHeader('Cache-Control', 'no-cache')
                ->withHeader('Last-Modified', $lastModified);

            $response->getBody()->write($contents);
            return $response;
        }
        return $handler->handle($request);
    }
}
