<?php

declare(strict_types=1);

namespace Remind\Headless\Preview;

use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ContentWithItemsPreviewRenderer extends StandardContentPreviewRenderer
{
    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        $out = parent::renderPageModulePreviewContent($item);

        $record = $item->getRecord();

        if ($record['tx_headless_item']) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getConnectionForTable('tx_headless_item')
                ->createQueryBuilder();
            $queryBuilder
                ->select('*')
                ->from('tx_headless_item')
                ->where($queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq(
                        'foreign_uid',
                        $queryBuilder->createNamedParameter($record['uid'], ParameterType::INTEGER)
                    ),
                    $queryBuilder->expr()->eq(
                        'foreign_table',
                        $queryBuilder->createNamedParameter('tt_content', ParameterType::STRING)
                    )
                ));

            $result = $queryBuilder->executeQuery();
            $rmndContentItems = $result->fetchAllAssociative();

            $itemContent = '<br />';

            $lastKey = array_key_last($rmndContentItems);

            foreach ($rmndContentItems as $key => $rmndContentItem) {
                if ($rmndContentItem['header']) {
                    $itemContent .= $this->renderItemHeader($rmndContentItem);
                }

                if ($rmndContentItem['bodytext']) {
                    $itemContent .= $this->renderText($rmndContentItem['bodytext']) . '<br />';
                }

                if ($rmndContentItem['image']) {
                    $fileReferences = BackendUtility::resolveFileReferences(
                        'tx_headless_item',
                        'image',
                        $rmndContentItem
                    );

                    if (!empty($fileReferences)) {
                        $itemContent .= $this->getThumbCodeUnlinked(current($fileReferences));
                        $itemContent .= '<br />';
                    }
                }

                if ($lastKey !== $key) {
                    $itemContent .= '<br />';
                }
            }

            $out .= $this->linkEditContent($itemContent, $record);
        }

        return $out;
    }

    /**
     * @param mixed[] $item
     */
    private function renderItemHeader(array $item): string
    {
        $outHeader = '';

        // Make header:
        if ($item['header']) {
            $hiddenHeaderNote = '';

            // If header layout is set to 'hidden', display an accordant note:
            if (((int) $item['header_layout']) === 100) {
                $hiddenHeaderNote = ' <em>[' . htmlspecialchars($this->getLanguageService()->sL(
                    'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.hidden'
                )) . ']</em>';
            }

            $outHeader .= '<strong>'
                        . $this->renderText($item['header'])
                        . $hiddenHeaderNote
                        . '</strong><br />';
        }

        return $outHeader;
    }
}
