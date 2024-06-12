<?php

declare(strict_types=1);

namespace Remind\Headless\Form;

use FriendsOfTYPO3\Headless\Form\Decorator\AbstractFormDefinitionDecorator;

class FormDefinitionDecorator extends AbstractFormDefinitionDecorator
{
    private const NOT_EMPTY_ERROR_CODES = [
        1221560910,
        1221560718,
        1347992400,
        1347992453,
    ];
    protected function overrideDefinition(array $decorated, array $definition, int $currentPage): array
    {
        foreach ($decorated['elements'] as &$element) {
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
        return $decorated;
    }
}
