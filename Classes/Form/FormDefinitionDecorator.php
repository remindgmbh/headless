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
            foreach ($element['properties']['links'] as $pageUid => $label) {
                $link = $this->cObj->createLink($label, ['parameter' => $pageUid]);
                if ($link instanceof LinkResult) {
                    $element['label'] = sprintf($element['label'], $link->getHtml());
                }
            }
            unset($element['properties']['links']);
        }
    }
}
