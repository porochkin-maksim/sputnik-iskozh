<?php declare(strict_types=1);

namespace Core\Db\Searcher\Models;

use Illuminate\Support\Collection;

class BaseSearchResponse
{
    private Collection $items;
    private ?int       $total;

    public function __construct()
    {
        $this->items = new Collection();
        $this->total = null;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function setItems(Collection $items): static
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getIds(): array
    {
        $result = [];
        try {
            foreach ($this->items as $item) {
                $result[] = $item->getId();
            }
        }
        catch (\Exception) {
        }

        return $result;
    }

    public function getTotal(): int
    {
        return $this->total ?? count($this->items);
    }

    public function setTotal(int $total): static
    {
        $this->total = $total;

        return $this;
    }
}
