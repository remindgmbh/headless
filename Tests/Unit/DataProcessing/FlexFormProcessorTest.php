<?php

declare(strict_types=1);

namespace Remind\Headless\Tests\Unit\DataProcessing;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Remind\Headless\DataProcessing\FlexFormProcessor;
use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentDataProcessor;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(FlexFormProcessor::class)]
class FlexFormProcessorTest extends UnitTestCase
{
    #[Test]
    public function processAppliesFallbackLinkParsingForUndeterminedType(): void
    {
        $GLOBALS['TCA']['tt_content']['columns']['pi_flexform'] = ['config' => []];

        $contentDataProcessor = $this->createMock(ContentDataProcessor::class);
        $contentDataProcessor
            ->method('process')
            ->willReturn([]);
        GeneralUtility::addInstance(ContentDataProcessor::class, $contentDataProcessor);

        $flexFormTools = $this->createMock(FlexFormTools::class);
        $flexFormTools
            ->method('getDataStructureIdentifier')
            ->willReturn('test-identifier');
        $flexFormTools
            ->method('parseDataStructureByIdentifier')
            ->willReturn([
                'elements' => [
                    'settings.cta' => [
                        'config' => [],
                    ],
                ],
            ]);
        GeneralUtility::addInstance(FlexFormTools::class, $flexFormTools);

        $flexFormService = $this->createMock(FlexFormService::class);
        $flexFormService
            ->method('convertFlexFormContentToArray')
            ->willReturn([
                'settings' => [
                    'cta' => 't3://page?uid=12',
                ],
            ]);
        GeneralUtility::setSingletonInstance(FlexFormService::class, $flexFormService);

        $cObj = $this->createMock(ContentObjectRenderer::class);
        $cObj
            ->method('stdWrapValue')
            ->willReturnMap([
                ['fieldName', [], '', ''],
                ['as', [], '', ''],
            ]);
        $cObj
            ->method('getCurrentTable')
            ->willReturn('tt_content');
        $cObj
            ->expects(self::once())
            ->method('typoLink')
            ->with('', ['parameter' => 't3://page?uid=12', 'returnLast' => 'result'])
            ->willReturn('/resolved/link');

        $processor = new FlexFormProcessor();

        $result = $processor->process($cObj, [], [], [
            'data' => [
                'pi_flexform' => 'xml',
            ],
        ]);

        self::assertSame('/resolved/link', $result['data']['pi_flexform']['settings']['cta']);
    }

    #[Test]
    public function processNormalizesNumericIndexesRecursively(): void
    {
        $GLOBALS['TCA']['tt_content']['columns']['pi_flexform'] = ['config' => []];

        $contentDataProcessor = $this->createMock(ContentDataProcessor::class);
        $contentDataProcessor
            ->method('process')
            ->willReturn([]);
        GeneralUtility::addInstance(ContentDataProcessor::class, $contentDataProcessor);

        $flexFormTools = $this->createMock(FlexFormTools::class);
        $flexFormTools
            ->method('getDataStructureIdentifier')
            ->willReturn('test-identifier');
        $flexFormTools
            ->method('parseDataStructureByIdentifier')
            ->willReturn([]);
        GeneralUtility::addInstance(FlexFormTools::class, $flexFormTools);

        $flexFormService = $this->createMock(FlexFormService::class);
        $flexFormService
            ->method('convertFlexFormContentToArray')
            ->willReturn([
                1 => [
                    'items' => [
                        1 => 'A',
                        2 => 'B',
                    ],
                ],
                2 => [
                    'items' => [
                        1 => 'C',
                    ],
                ],
            ]);
        GeneralUtility::setSingletonInstance(FlexFormService::class, $flexFormService);

        $cObj = $this->createMock(ContentObjectRenderer::class);
        $cObj
            ->method('stdWrapValue')
            ->willReturnMap([
                ['fieldName', [], '', ''],
                ['as', [], '', ''],
            ]);
        $cObj
            ->method('getCurrentTable')
            ->willReturn('tt_content');
        $cObj
            ->expects(self::never())
            ->method('typoLink');

        $processor = new FlexFormProcessor();

        $result = $processor->process($cObj, [], [], [
            'data' => [
                'pi_flexform' => 'xml',
            ],
        ]);

        self::assertSame(
            [
                [
                    'items' => ['A', 'B'],
                ],
                [
                    'items' => ['C'],
                ],
            ],
            $result['data']['pi_flexform']
        );
    }

    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();
        unset($GLOBALS['TCA']['tt_content']['columns']['pi_flexform']);
        parent::tearDown();
    }
}
