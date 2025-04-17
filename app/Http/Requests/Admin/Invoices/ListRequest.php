<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Invoices;

use App\Http\Requests\DefaultRequest;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;

class ListRequest extends DefaultRequest
{
    public function rules(): array
    {
        return [
            'limit'        => 'integer|min:1|max:100',
            'skip'         => 'integer|min:0',
            'type'         => 'integer|in:0,' . implode(',', InvoiceTypeEnum::values()),
            'period_id'    => 'integer|min:0',
            'account_id'   => 'integer|min:0',
            'account'      => 'nullable|string|max:255',
            'payed_status' => 'string|in:all,payed,unpayed,partial',
            'sort_field'   => 'string|in:id,cost,payed,updated_at',
            'sort_order'   => 'string|in:asc,desc',
        ];
    }

    public function getLimit(): int
    {
        return (int)$this->input('limit', 25);
    }

    public function getOffset(): int
    {
        return (int)$this->input('skip', 0);
    }

    public function getType(): ?int
    {
        return $this->input('type') ? (int)$this->input('type') : null;
    }

    public function getPeriodId(): ?int
    {
        return $this->input('period_id') ? (int)$this->input('period_id') : null;
    }

    public function getAccountId(): ?int
    {
        return $this->input('account_id') ? (int)$this->input('account_id') : null;
    }

    public function getAccount(): ?string
    {
        return $this->input('account');
    }

    public function getPayedStatus(): ?string
    {
        return $this->input('payed_status');
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
