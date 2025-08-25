<?php declare(strict_types=1);

namespace Core\Db\Searcher;

use App\Models\User;
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

    private ?bool $withTrashed = null;

    /** @var int[] $ids */
    private ?array $ids    = null;
    private array  $select = [];
    private array  $with   = [];
    /** @var string[] $groupBy */
    private array $groupBy = [];

    private ?WhereCollection $where       = null;
    private ?WhereCollection $orWhere     = null;
    private ?WhereCollection $whereColumn = null;
    private array            $whereIn     = [];

    private ?string $search = null;

    public static function make(): static
    {
        return new static();
    }

    public function __serialize(): array
    {
        return [
            'sortOrderProperties' => $this->sortOrderProperties,

            'limit'  => $this->limit,
            'offset' => $this->offset,
            'lastId' => $this->lastId,

            'ids'    => $this->ids,
            'select' => $this->select,
            'with'   => $this->with,

            'where'   => $this->where,
            'whereIn' => $this->whereIn,

            'search' => $this->search,
        ];
    }

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

    public function setSelect(array $select): static
    {
        $this->select = $select;

        return $this;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setSearch(?string $search): static
    {
        $this->search = $search;

        return $this;
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

    /** Выборка */
    public function getOrWhere(): WhereCollection
    {
        if ( ! isset($this->orWhere)) {
            $this->orWhere = new WhereCollection();
        }

        return $this->orWhere;
    }

    /** Выборка */
    public function getWhereColumn(): WhereCollection
    {
        if ( ! isset($this->whereColumn)) {
            $this->whereColumn = new WhereCollection();
        }

        return $this->whereColumn;
    }

    public function addWhere(string $field, string $operator, mixed $value = null): static
    {
        $this->getWhere()->push(new Where($field, $operator, $value));

        return $this;
    }

    public function addOrWhere(string $field, string $operator, mixed $value = null): static
    {
        $this->getOrWhere()->push(new Where($field, $operator, $value));

        return $this;
    }

    public function addWhereColumn(string $field1, string $operator, mixed $field2 = null): static
    {
        $this->getWhereColumn()->push(new Where($field1, $operator, $field2));

        return $this;
    }

    /**
     * @return Where[]
     */
    public function getWhereIn(): array
    {
        return $this->whereIn;
    }

    public function getGroupsBy(): array
    {
        return $this->groupBy;
    }

    public function addGroupBy(string $groupBy): static
    {
        $this->groupBy[] = $groupBy;

        return $this;
    }

    public function setWithDeleted(?bool $flag = true): static
    {
        $this->withTrashed = $flag;

        return $this;
    }

    public function getWithTrashed(): ?bool
    {
        return $this->withTrashed;
    }
}
