<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Counters;

use App\Http\Requests\AbstractRequest;
use App\Models\Counter\Counter;
use App\Models\Counter\CounterHistory;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Validation\Rule;

class LinkRequest extends AbstractRequest
{
    private const ID         = RequestArgumentsEnum::ID;
    private const COUNTER_ID = RequestArgumentsEnum::COUNTER_ID;

    public function rules(): array
    {
        return [
            self::ID         => [
                'required',
                Rule::exists(CounterHistory::TABLE, CounterHistory::ID),
            ],
            self::COUNTER_ID => [
                'required',
                Rule::exists(Counter::TABLE, Counter::ID),
            ],
        ];
    }

    public function messages(): array
    {
        return [

        ];
    }

    public function getId(): ?int
    {
        return $this->getIntOrNull(self::ID);
    }

    public function getCounterId(): ?int
    {
        return $this->getIntOrNull(self::COUNTER_ID);
    }
}
