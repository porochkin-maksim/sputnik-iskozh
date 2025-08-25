<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Accounts;

use App\Http\Requests\DefaultRequest;
use App\Http\Requests\SortFieldTrait;
use Core\Requests\RequestArgumentsEnum;

class ListRequest extends DefaultRequest
{
    use SortFieldTrait;

    public function rules(): array
    {
        return [
            'limit'        => 'integer|min:1|max:1000',
            'skip'         => 'integer|min:0',
            'sort_field'   => 'string|in:id,sort_value,size',
            'sort_order'   => 'string|in:asc,desc',
        ];
    }

    private const string ACCOUNT_ID = RequestArgumentsEnum::ACCOUNT_ID;

    public function getAccountId(): ?int
    {
        return $this->getIntOrNull(self::ACCOUNT_ID);
    }
}
