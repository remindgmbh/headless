<?php

declare(strict_types=1);

namespace Remind\Headless\TCA;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\ConnectionPool;
use Doctrine\DBAL\ForwardCompatibility\Result;

class DisplayCond
{
    public function parentIsRoot($args): bool
    {
        ['record' => $record] = $args;

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $qb = $queryBuilder
            ->select('is_siteroot')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($record['pid'], \PDO::PARAM_INT)),
            );

        /** @var Result */
        $result = $qb->execute();

        return (bool)$result->fetchOne();
    }
}
