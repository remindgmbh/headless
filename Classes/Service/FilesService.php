<?php

declare(strict_types=1);

namespace Remind\Headless\Service;

use FriendsOfTYPO3\Headless\Utility\File\ProcessingConfiguration;
use FriendsOfTYPO3\Headless\Utility\FileUtility;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\ImageService;

class FilesService
{
    private FileRepository $fileRepository;

    private FileUtility $fileUtility;

    private ImageService $imageService;

    /** @var mixed[] $defaultConfiguration */
    private array $defaultConfiguration;

    public function __construct()
    {
        $request = $this->getRequest();
        $frontendTypoScript = $request->getAttribute('frontend.typoscript');
        $fullTypoScript = $frontendTypoScript?->getSetupArray();

        $this->defaultConfiguration = $fullTypoScript ? $fullTypoScript['lib.']['assetProcessingConfiguration.'] : [];
        $this->fileUtility = GeneralUtility::makeInstance(FileUtility::class);
        $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        $this->imageService = GeneralUtility::makeInstance(ImageService::class);
    }

    /**
     * @param mixed[] $configuration
     * @return mixed[]
     */
    public function processImage(int $uid, array $configuration = []): array
    {
        $processingConfiguration = $this->getProcessingConfiguration($configuration);
        $imageObj = $this->imageService->getImage(strval($uid), null, true);
        return $this->fileUtility->process($imageObj, $processingConfiguration);
    }

    /**
     * @param mixed[] $configuration
     * @return ?mixed[]
     */
    public function processImages(string $tableName, string $fieldName, int $uid, array $configuration = []): ?array
    {
        $processingConfiguration = $this->getProcessingConfiguration($configuration);

        /** @var \TYPO3\CMS\Core\Resource\FileInterface[] $fileObjects */
        $fileObjects = $this->fileRepository->findByRelation($tableName, $fieldName, $uid);

        $processedFiles = [];

        foreach ($fileObjects as $fileObject) {
            $processedFiles[] = $this->fileUtility->process($fileObject, $processingConfiguration);
        }

        return $processingConfiguration->flattenObject ? $processedFiles[0] ?? null : $processedFiles;
    }

    /**
     * @param mixed[] $overrule
     */
    private function getProcessingConfiguration(array $overrule = []): ProcessingConfiguration
    {
        $configuration = $this->defaultConfiguration;

        ArrayUtility::mergeRecursiveWithOverrule($configuration, $overrule);

        return ProcessingConfiguration::fromOptions($configuration);
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
