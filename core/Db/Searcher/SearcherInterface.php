<?php declare(strict_types=1);

namespace Core\Db\Searcher;

use Core\Db\Searcher\Collections\WhereCollection;
use Core\Db\Searcher\Models\Order;

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
    public const IS_NOT  = '!=';
    public const NOT_IN  = 'NOT_IN';
    public const IN      = 'IN';

    public function getIds(): ?array;

    /**
     * @return Order[]
     */
    public function getSortProperties(): array;

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
