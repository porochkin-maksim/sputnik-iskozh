<?php declare(strict_types=1);

namespace App\Http\Requests;

trait SortFieldTrait
{
    public function getSortField(): ?string
    {
        return $this->input('sort_field', 'id');
    }

    public function getSortOrder(): ?string
    {
        return $this->input('sort_order', 'desc');
    }
}