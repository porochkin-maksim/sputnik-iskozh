<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Users;

use App\Http\Requests\DefaultRequest;
use App\Http\Requests\SortFieldTrait;

class ListRequest extends DefaultRequest
{
    use SortFieldTrait;

    public function rules(): array
    {
        return [
            'limit'        => 'integer|min:1|max:100',
            'skip'         => 'integer|min:0',
            'sort_field'   => 'string|in:id,last_name,first_name,middle_name,email,account_sort',
            'sort_order'   => 'string|in:asc,desc',
        ];
    }
}
