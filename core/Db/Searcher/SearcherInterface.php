<?php declare(strict_types=1);

namespace Core\Db\Searcher;

use Core\Db\Searcher\Collections\WhereCollection;
use Core\Db\Searcher\Models\Order;

interface SearcherInterface
{
    public const string SORT_ORDER_ASC  = 'asc';
    public const string SORT_ORDER_DESC = 'desc';

    public const string EQUALS      = '=';
    public const string IS_NULL     = 'IS NULL';
    public const string IS_NOT_NULL = 'NOT NULL';
    public const string GT          = '>';
    public const string GTE         = '>=';
    public const string LT          = '<';
    public const string LTE         = '<=';
    public const string IS_NOT      = '!=';
    public const string NOT_IN      = 'NOT_IN';
    public const string IN          = 'IN';
    public const string LIKE        = 'like';

    public function getIds(): ?array;

    /**
     * @param int[] $ids
     */
    public function setIds(array $ids): static;

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

    public function getOrWhere(): WhereCollection;

    public function getWhereColumn(): WhereCollection;

    /**
     * @return string[]
     */
    public function getGroupsBy(): array;
}
