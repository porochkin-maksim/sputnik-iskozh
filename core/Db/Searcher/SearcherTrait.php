<?php declare(strict_types=1);

namespace Core\Db\Searcher;

use Core\Db\Searcher\Collections\WhereCollection;
use Core\Db\Searcher\Models\Where;

trait SearcherTrait
{
    private string $sortOrder;
    private string $sortOrderProperty = 'id';

    /** @var int[] $ids */
    private array $ids    = [];
    private array $select = [];
    private array $with   = [];

    private WhereCollection $where;
    private array           $whereIn = [];

    /**
     * @return int[]
     */
    public function getIds(): array
    {
        return $this->ids;
    }

    /**
     * @param int[] $ids
     */
    public function setIds(array $ids): static
    {
        $this->ids = $ids;

        return $this;
    }

    public function getSelect(): array
    {
        return $this->select;
    }

    public function setSortOrderAsc(): static
    {
        $this->sortOrder = SearcherInterface::SORT_ORDER_ASC;

        return $this;
    }

    public function setSortOrderDesc(): static
    {
        $this->sortOrder = SearcherInterface::SORT_ORDER_DESC;

        return $this;
    }

    public function getSortOrder(): string
    {
        return $this->sortOrder ?? SearcherInterface::SORT_ORDER_ASC;
    }

    public function getSortProperty(): string
    {
        return $this->sortOrderProperty;
    }

    public function setSortOrderProperty(string $sortOrderProperty): static
    {
        $this->sortOrderProperty = $sortOrderProperty;

        return $this;
    }

    public function getWith(): array
    {
        return array_unique($this->with);
    }

    public function getWhere(): WhereCollection
    {
        if (!isset($this->where)) {
            $this->where = new WhereCollection();
        }
        return $this->where;
    }

    public function addWhere(string $type, string $operator, mixed $value = null): static
    {
        $this->getWhere()->push(new Where($type, $operator, $value));

        return $this;
    }

    /**
     * @return Where[]
     */
    public function getWhereIn(): array
    {
        return $this->whereIn;
    }
}
