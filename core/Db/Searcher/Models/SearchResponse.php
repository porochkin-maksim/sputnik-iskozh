<?php declare(strict_types=1);

namespace Core\Db\Searcher\Models;

use Illuminate\Support\Collection;

class SearchResponse
{
    private Collection $items;
    private int  $total;

    public function __construct()
    {
        $this->items = new Collection();
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

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): static
    {
        $this->total = $total;

        return $this;
    }
}
