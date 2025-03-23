<?php declare(strict_types=1);

namespace App\Http\Requests\Profile\Counters;

use App\Http\Requests\AbstractRequest;
use App\Models\Counter\Counter;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

class AddHistoryRequest extends AbstractRequest
{
    private const COUNTER_ID = 'counter_id';
    private const VALUE      = 'value';
    private const FILE       = 'file';

    public function rules(): array
    {
        $counter = 1;

        return [
            self::COUNTER_ID => [
                'required',
                Rule::exists(Counter::TABLE, Counter::ID),
            ],
            self::VALUE      => [
                'required',
                'min:0',
            ],
        ];
    }

    public function messages(): array
    {
        return [
        ];
    }

    public function getCounterId(): int
    {
        return $this->getInt(self::COUNTER_ID);
    }

    public function getValue(): int
    {
        return $this->getInt(self::VALUE);
    }

    public function getFile(): UploadedFile
    {
        return $this->file(self::FILE);
    }
}
