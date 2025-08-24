<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Users;

use App\Http\Requests\DefaultRequest;
use App\Http\Requests\SortFieldTrait;
use Symfony\Component\HttpFoundation\ParameterBag;

class ListRequest extends DefaultRequest
{
    use SortFieldTrait;

    public function rules(): array
    {
        return [
            'limit'        => 'integer|min:1|max:100',
            'skip'         => 'integer|min:0',
            'sort_field'   => 'string|in:id,last_name,first_name,middle_name,email',
            'sort_order'   => 'string|in:asc,desc',
        ];
    }

    public function messages()
    {
        $result = parent::messages();

        $result['sort_field.in'] = 'Недопустимое значение для свойства сортировки. Попробуйте перейти на страницу по ссылке из меню';

        return $result;
    }
}
