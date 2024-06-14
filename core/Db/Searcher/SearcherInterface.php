<?php declare(strict_types=1);

namespace Core\Db\Searcher;

use Core\Db\Searcher\Collections\WhereCollection;

interface SearcherInterface
{
    public const SORT_ORDER_ASC  = 'asc';
    public const SORT_ORDER_DESC = 'desc';

    public const EQUALS  = '=';
    public const IS_NULL = 'IS NULL';
    public const GT      = '>';
    public const GTE     = '>=';
    public const LT      = '<';
    public const LTE     = '<=';

    public function getIds(): ?array;

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
