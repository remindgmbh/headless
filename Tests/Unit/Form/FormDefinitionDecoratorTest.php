<?php

declare(strict_types=1);

namespace Remind\Headless\Tests\Unit\Form;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Remind\Headless\Form\FormDefinitionDecorator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Typolink\LinkResult;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(FormDefinitionDecorator::class)]
class FormDefinitionDecoratorTest extends UnitTestCase
{
    #[Test]
    public function invokeAppliesCoreElementTransformations(): void
    {
        $cObj = $this->createMock(ContentObjectRenderer::class);
        $cObj
            ->expects(self::exactly(2))
            ->method('createLink')
            ->willReturnCallback(static function (string $label, array $configuration): LinkResult {
                return (new LinkResult('page', '/' . $configuration['parameter']))
                    ->withLinkText($label);
            });
        GeneralUtility::addInstance(ContentObjectRenderer::class, $cObj);

        $definition = [
            'identifier' => 'testForm',
            'renderables' => [
                0 => [
                    'renderables' => [
                        [
                            'identifier' => 'privacy',
                            'label' => 'Please accept %s and %s',
                            'properties' => [
                                'elementDescription' => 'Details: %s | %s',
                                'fluidAdditionalAttributes' => ['required' => 'required'],
                                'links' => [
                                    12 => 'Privacy Policy',
                                    34 => 'Terms and Conditions',
                                ],
                                'validationErrorMessages' => [
                                    ['code' => 1221560910, 'customMessage' => 'This field is required'],
                                    ['code' => 999, 'customMessage' => 'ignore'],
                                ],
                            ],
                            'type' => 'Checkbox',
                            'validators' => [
                                ['identifier' => 'NotEmpty'],
                                [
                                    'identifier' => 'FileSize',
                                    'options' => [
                                        'maximum' => '3M',
                                        'minimum' => '2K',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'identifier' => 'upload',
                            'properties' => [
                                'allowedMimeTypes' => ['application/pdf', 'image/png'],
                            ],
                            'type' => 'FileUpload',
                            'validators' => [],
                        ],
                    ],
                ],
            ],
        ];

        $result = (new FormDefinitionDecorator())($definition, 0);
        $checkbox = $result['elements'][0];
        $fileUpload = $result['elements'][1];

        self::assertStringContainsString('<a href="/12">Privacy Policy</a>', $checkbox['label']);
        self::assertStringContainsString('<a href="/34">Terms and Conditions</a>', $checkbox['label']);
        self::assertStringContainsString('<a href="/12">Privacy Policy</a>', $checkbox['properties']['elementDescription']);
        self::assertStringContainsString('<a href="/34">Terms and Conditions</a>', $checkbox['properties']['elementDescription']);
        self::assertArrayNotHasKey('links', $checkbox['properties']);
        self::assertArrayNotHasKey('fluidAdditionalAttributes', $checkbox['properties']);
        self::assertSame('This field is required', $checkbox['validators'][0]['customErrorMessage']);
        self::assertSame(2048, $checkbox['validators'][1]['options']['minimum']);
        self::assertSame(3145728, $checkbox['validators'][1]['options']['maximum']);
        self::assertSame(
            [['code' => 999, 'customMessage' => 'ignore']],
            array_values($checkbox['properties']['validationErrorMessages'])
        );

        self::assertSame('MimeType', $fileUpload['validators'][0]['identifier']);
        self::assertSame(['application/pdf', 'image/png'], $fileUpload['validators'][0]['options']['allowed']);
    }

    #[Test]
    public function invokeDoesNotAppendMimeTypeValidatorWithoutAllowedMimeTypes(): void
    {
        GeneralUtility::addInstance(ContentObjectRenderer::class, $this->createMock(ContentObjectRenderer::class));

        $definition = [
            'identifier' => 'testForm',
            'renderables' => [
                0 => [
                    'renderables' => [
                        [
                            'identifier' => 'upload',
                            'properties' => [],
                            'type' => 'FileUpload',
                            'validators' => [
                                ['identifier' => 'NotEmpty'],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $result = (new FormDefinitionDecorator())($definition, 0);
        self::assertCount(1, $result['elements'][0]['validators']);
        self::assertSame('NotEmpty', $result['elements'][0]['validators'][0]['identifier']);
    }

    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();
        parent::tearDown();
    }
}
