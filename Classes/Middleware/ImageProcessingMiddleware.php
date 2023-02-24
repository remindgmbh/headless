<?php

declare(strict_types=1);

namespace Remind\Headless\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\ImageService;

class ImageProcessingMiddleware implements MiddlewareInterface
{
    private ResponseFactoryInterface $responseFactory;

    private ImageService $imageService;

    public function __construct()
    {
        $this->imageService = GeneralUtility::makeInstance(ImageService::class);
    }

    public function injectResponseFactory(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var \TYPO3\CMS\Core\Routing\SiteRouteResult $routing */
        $routing = $request->getAttribute('routing');
        $path = $routing->getUri()->getPath();
        $queryParams = $request->getQueryParams();
        $uid = $queryParams['uid'] ?? null;
        if ($path === '/image' && $uid) {
            $image = $this->imageService->getImage($uid, null, true);

            $lastModified = gmdate('D, d M Y H:i:s', $image->getProperty('tstamp')) . ' GMT';
            $ifModifiedSince = $request->getHeader('if-modified-since')[0] ?? null;

            if ($lastModified === $ifModifiedSince) {
                return $this->responseFactory->createResponse(304);
            }

            $processingInstructions = [
                'width' => $queryParams['width'] ?? null,
                'height' => $queryParams['height'] ?? null,
                'maxWidth' => $queryParams['maxWidth'] ?? null,
                'maxHeight' => $queryParams['maxHeight'] ?? null,
                'fileExtension' => $queryParams['fileExtension'] ?? null,
            ];

            $processedImage = $this->imageService->applyProcessingInstructions($image, $processingInstructions);

            $mimeType = $processedImage->getMimeType();
            $contents = $processedImage->getContents();

            $response = $this->responseFactory
                ->createResponse()
                ->withHeader('Content-Type', $mimeType)
                ->withHeader('Cache-Control', 'no-cache')
                ->withHeader('Last-Modified', $lastModified);

            $response->getBody()->write($contents);
            return $response;
        }
        return $handler->handle($request);
    }
}
