<?php declare(strict_types=1);

namespace Core\Db\Searcher;

use Core\Db\Searcher\Collections\WhereCollection;
use Core\Db\Searcher\Models\Where;

interface SearcherInterface
{
    public const SORT_ORDER_ASC  = 'asc';
    public const SORT_ORDER_DESC = 'desc';

    public const EQUALS  = '=';
    public const IS_NULL = 'IS NULL';

    public function getSortOrder(): string;

    public function getSortProperty(): string;

    public function getLimit(): ?int;

    public function getOffset(): ?int;

    public function getLastId(): ?int;

    /**
     * @return string[]
     */
    public function getWith(): array;

    /**
     * @return string[]
     */
    public function getSelect(): array;

    public function getWhere(): WhereCollection;
}
