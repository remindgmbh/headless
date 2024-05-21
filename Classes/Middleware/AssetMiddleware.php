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
use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Service\ImageService;

class AssetMiddleware implements MiddlewareInterface
{
    private ResponseFactoryInterface $responseFactory;
    private ImageService $imageService;
    private ResourceFactory $resourceFactory;

    public function injectImageService(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function injectResponseFactory(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function injectResourceFactory(ResourceFactory $resourceFactory)
    {
        $this->resourceFactory = $resourceFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var \TYPO3\CMS\Core\Routing\SiteRouteResult $routing */
        $routing = $request->getAttribute('routing');
        $path = $routing->getUri()->getPath();
        $queryParams = $request->getQueryParams();
        $uid = $queryParams['uid'] ?? null;
        if ($path === '/asset' && $uid) {
            $resource = $this->resourceFactory->getFileReferenceObject($uid);

            $tstamp = intval($resource->getProperty('tstamp'));
            $lastModified = gmdate('D, d M Y H:i:s', $tstamp) . ' GMT';
            $ifModifiedSince = $request->getHeader('if-modified-since')[0] ?? null;

            if ($lastModified === $ifModifiedSince) {
                return $this->responseFactory->createResponse(304);
            }

            $type = (int) $resource->getProperty('type');

            $processedResource = $resource;

            if ($type === AbstractFile::FILETYPE_IMAGE) {
                $targetFileExtension = $queryParams['fileExtension'] ?? null;

                // Skip processing for SVGs without changing image type
                if ($resource->getExtension() !== 'svg' || (!$targetFileExtension || $targetFileExtension === 'svg')) {
                    $cropVariant = $queryParams['breakpoint'] ?? 'default';

                    $crop = $resource->getProperty('crop');
                    $cropVariantCollection = CropVariantCollection::create($crop);
                    $cropArea = $cropVariantCollection->getCropArea($cropVariant);

                    // Use default cropVariant if breakpoint cropVariant does not exist
                    if ($cropArea == Area::createEmpty()) {
                        $cropArea = $cropVariantCollection->getCropArea();
                    }

                    $processingInstructions = [
                        'width' => $queryParams['width'] ?? null,
                        'height' => $queryParams['height'] ?? null,
                        'maxWidth' => $queryParams['maxWidth'] ?? null,
                        'maxHeight' => $queryParams['maxHeight'] ?? null,
                        'fileExtension' => $queryParams['fileExtension'] ?? null,
                        'crop' => $cropArea->makeAbsoluteBasedOnFile($resource),
                    ];

                    $processedResource = $this->imageService->applyProcessingInstructions($resource, $processingInstructions);
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
