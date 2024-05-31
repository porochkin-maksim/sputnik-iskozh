<?php declare(strict_types=1);

namespace Core\Db\Searcher;

interface SearcherInterface
{
    public const SORT_ORDER_ASC  = 'asc';
    public const SORT_ORDER_DESC = 'desc';

    public function getSortOrder(): string;

    public function getSortProperty(): string;

    public function getWith(): array;

    public function getSelect(): array;
}
