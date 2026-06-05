<?php declare(strict_types=1);

namespace Core\Repositories;

use Core\Repositories\Models\SortOrder;
use Core\Repositories\Models\SortOrderCollection;
use Core\Repositories\Models\Where;
use Core\Repositories\Models\WhereCollection;

trait SearcherTrait
{

    protected ?int $limit  = null;
    protected ?int $offset = null;
    protected ?int $lastId = null;

    protected ?bool $withTrashed = null;

    /** @var int[] $ids */
    protected ?array $ids    = null;
    protected array  $select = [];
    protected array  $with   = [];
    /** @var string[] $groupBy */
    protected array $groupBy = [];

    protected ?SortOrderCollection $sortOrderProperties = null;

    protected ?WhereCollection $where       = null;
    protected ?WhereCollection $orWhere     = null;
    protected ?WhereCollection $whereColumn = null;
    protected ?WhereCollection $whereIn     = null;

    protected ?string $search = null;

    public static function make(): static
    {
        return new static();
    }

    public function __serialize(): array
    {
        // Получаем все нестатичные свойства текущего класса/трейта
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
        $data = [];
        foreach ($properties as $prop) {
            $prop->setAccessible(true);
            $data[$prop->getName()] = $prop->getValue($this);
        }
        return $data;
    }

    public function __unserialize(array $data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
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

    /**
     * Сортировки
     */

    public function getSortProperties(): SortOrderCollection
    {
        if ( ! isset($this->sortOrderProperties)) {
            $this->sortOrderProperties = new SortOrderCollection();
        }

        return $this->sortOrderProperties;
    }

    public function setSortOrderProperty(string $sortOrderProperty, string $order): static
    {
        $this->getSortProperties()->push(new SortOrder($sortOrderProperty, $order));

        return $this;
    }

    public function setSortOrderPropertyIdDesc(): static
    {
        $this->getSortProperties()->push(new SortOrder('id', SearcherInterface::SORT_ORDER_DESC));

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

    /** Выборка */
    public function getWhereIn(): WhereCollection
    {
        if ( ! isset($this->whereIn)) {
            $this->whereIn = new WhereCollection();
        }

        return $this->whereIn;
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

    public function addWhereIn(string $field, mixed $value = null): static
    {
        $this->getWhereIn()->push(new Where($field, '', $value));

        return $this;
    }

    public function addWhereColumn(string $field1, string $operator, mixed $field2 = null): static
    {
        $this->getWhereColumn()->push(new Where($field1, $operator, $field2));

        return $this;
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
