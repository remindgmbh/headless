<?php

declare(strict_types=1);

namespace Remind\Headless\LinkHandler;

use TYPO3\CMS\Frontend\Typolink\AbstractTypolinkBuilder;
use TYPO3\CMS\Frontend\Typolink\LinkResult;
use TYPO3\CMS\Frontend\Typolink\LinkResultInterface;

class CookiesLinkBuilder extends AbstractTypolinkBuilder
{
    /**
     * @param mixed[] $linkDetails
     * @param mixed[] $conf
     */
    public function build(
        array &$linkDetails,
        string $linkText,
        string $target,
        array $conf,
    ): LinkResultInterface {
        $action = $linkDetails['action'];
        $url = 't3://cookies?action=' . $action;

        return (new LinkResult('cookies', $url))
            ->withTarget($target)
            ->withLinkConfiguration($conf)
            ->withLinkText($linkText);
    }
}
