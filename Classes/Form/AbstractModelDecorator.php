<?php

declare(strict_types=1);

namespace Remind\Headless\Form;

use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractModelDecorator extends FormDefinitionDecorator
{
    protected string $actionName = '';
    protected string $controllerName = '';
    protected string $valueName = '';

    protected function overrideDefinition(array $decorated, array $definition, int $currentPage): array
    {
        $decorated = parent::overrideDefinition($decorated, $definition, $currentPage);

        $request = $this->getRequest();

        /** @var \TYPO3\CMS\Core\Routing\PageArguments $pageArguments */
        $pageArguments = $request->getAttribute('routing');

        $arguments = $pageArguments->getArguments();

        $controllerArguments = $arguments[$this->controllerName] ?? null;

        if ($controllerArguments) {
            $uid = (int) $controllerArguments[$this->valueName] ?? null;
            $action = $controllerArguments['action'] ?? null;

            if ($action === $this->actionName && $uid) {
                $decorated['elements'][] = [
                    'type' => 'Hidden',
                    'defaultValue' => $uid,
                    'name' => $this->controllerName . '[' . $this->valueName . ']',
                ];
            }
        }

        return $decorated;
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
