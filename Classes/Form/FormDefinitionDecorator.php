<?php

declare(strict_types=1);

namespace Remind\Headless\Form;

use FriendsOfTYPO3\Headless\Form\Decorator\AbstractFormDefinitionDecorator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Typolink\LinkResult;

class FormDefinitionDecorator extends AbstractFormDefinitionDecorator
{
    private const NOT_EMPTY_ERROR_CODES = [
        1221560910,
        1221560718,
        1347992400,
        1347992453,
    ];

    private ContentObjectRenderer $cObj;

    /**
     * @param mixed[] $formStatus
     */
    public function __construct(array $formStatus = [])
    {
        parent::__construct($formStatus);
        $this->cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     * @param mixed[] $decorated
     * @param mixed[] $definition
     * @return mixed[]
     */
    protected function overrideDefinition(array $decorated, array $definition, int $currentPage): array
    {
        foreach ($decorated['elements'] as &$element) {
            $this->setNotEmptyValidationErrorMessages($element);
            $this->setCheckboxLinks($element);
            $this->setFileSizeValidatorBytes($element);
            $this->setMimeTypeValidator($element);
        }
        return $decorated;
    }

    /**
     * @param mixed[] $element
     */
    private function setNotEmptyValidationErrorMessages(array &$element): void
    {
        $notEmptyValidators = array_filter($element['validators'] ?? [], function (array $validator) {
            return $validator['identifier'] === 'NotEmpty';
        });
        if ($notEmptyValidators) {
            foreach ($element['properties']['validationErrorMessages'] ?? [] as $validationErrorMessageKey => $validationErrorMessage) {
                if (in_array($validationErrorMessage['code'], self::NOT_EMPTY_ERROR_CODES)) {
                    foreach (array_keys($notEmptyValidators) as $validatorKey) {
                        $element['validators'][$validatorKey]['customErrorMessage'] = $validationErrorMessage['customMessage'];
                    }
                    unset($element['properties']['validationErrorMessages'][$validationErrorMessageKey]);
                }
            }
            if (empty($element['properties']['validationErrorMessages'])) {
                unset($element['properties']['validationErrorMessages']);
            }
        }
        unset($element['properties']['fluidAdditionalAttributes']);
    }

    /**
     * @param mixed[] $element
     */
    private function setCheckboxLinks(array &$element): void
    {
        if (
            $element['type'] === 'Checkbox' &&
            isset($element['properties']['links'])
        ) {
            $links = array_map(function ($pageUid, $label) {
                $link = $this->cObj->createLink($label, ['parameter' => $pageUid]);
                return $link instanceof LinkResult ? $link->getHtml() : null;
            }, array_keys($element['properties']['links']), $element['properties']['links']);

            $element['label'] = sprintf($element['label'], ...$links);
            if (isset($element['properties']['elementDescription'])) {
                $element['properties']['elementDescription'] = sprintf($element['properties']['elementDescription'], ...$links);
            }

            unset($element['properties']['links']);
        }
    }

    /**
     * @param mixed[] $element
     */
    private function setFileSizeValidatorBytes(array &$element): void
    {
        foreach ($element['validators'] ?? [] as $key => $value) {
            if ($value['identifier'] === 'FileSize') {
                $element['validators'][$key]['options']['minimum'] = GeneralUtility::getBytesFromSizeMeasurement($value['options']['minimum']);
                $element['validators'][$key]['options']['maximum'] = GeneralUtility::getBytesFromSizeMeasurement($value['options']['maximum']);
            }
        }
    }

    /**
     * @param mixed[] $element
     */
    private function setMimeTypeValidator(array &$element): void
    {
        if ($element['type'] === 'FileUpload') {
            $mimeTypes = $element['properties']['allowedMimeTypes'] ?? [];

            if (!empty($mimeTypes)) {
                $element['validators'][] = [
                    'identifier' => 'MimeType',
                    'options' => [
                        'allowed' => $mimeTypes,
                    ],
                ];
            }
        }
    }
}
