<?php declare(strict_types=1);

namespace Core\Db\Searcher;

use Core\Db\Searcher\Collections\WhereCollection;
use Core\Db\Searcher\Models\Order;
use Core\Db\Searcher\Models\Where;

trait SearcherTrait
{
    /** @var Order[] */
    private array $sortOrderProperties = [];

    private ?int $limit  = null;
    private ?int $offset = null;
    private ?int $lastId = null;

    /** @var int[] $ids */
    private ?array $ids    = null;
    private array  $select = [];
    private array  $with   = [];

    private WhereCollection $where;
    private array           $whereIn = [];

    /**
     * @return null|int[]
     */
    public function getIds(): ?array
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

    public function setId(int|string|null $id): static
    {
        $this->ids = $id ? [(int) $id] : [-1];

        return $this;
    }

    public function getSelect(): array
    {
        return $this->select;
    }

    /** Сортировки */

    /**
     * @return Order[]
     */
    public function getSortProperties(): array
    {
        return $this->sortOrderProperties;
    }

    public function setSortOrderProperty(string $sortOrderProperty, string $order): static
    {
        $this->sortOrderProperties[] = new Order($sortOrderProperty, $order);

        return $this;
    }

    /** Отступы и лимиты */

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function setOffset(?int $offset): static
    {
        $this->offset = $offset;

        return $this;
    }

    public function getLastId(): ?int
    {
        return $this->lastId;
    }

    public function setLastId(?int $lastId): static
    {
        $this->lastId = $lastId;

        return $this;
    }

    /** Отношения */

    public function getWith(): array
    {
        return array_unique($this->with);
    }

    /** Выборка */

    public function getWhere(): WhereCollection
    {
        if ( ! isset($this->where)) {
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
