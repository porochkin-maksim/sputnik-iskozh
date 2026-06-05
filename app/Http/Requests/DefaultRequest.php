<?php declare(strict_types=1);

namespace App\Http\Requests;

class DefaultRequest extends AbstractRequest
{
    private const string LIMIT  = 'limit';
    private const string SKIP   = 'skip';
    private const string SEARCH = 'search';

    public function getLimit(): ?int
    {
        return $this->getIntOrNull(self::LIMIT);
    }

    public function getOffset(): ?int
    {
        return $this->getIntOrNull(self::SKIP);
    }

    public function getSearch(): ?string
    {
        return $this->getStringOrNull(self::SEARCH);
    }

    public function getSortField(): ?string
    {
        return $this->input('sort_field', 'id');
    }

    public function getSortOrder(): ?string
    {
        return $this->input('sort_order', 'desc');
    }
}
