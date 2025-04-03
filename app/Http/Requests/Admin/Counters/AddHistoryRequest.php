<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Counters;

use App\Http\Requests\AbstractRequest;
use App\Models\Counter\Counter;
use Carbon\Carbon;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

class AddHistoryRequest extends AbstractRequest
{
    private const ID         = RequestArgumentsEnum::ID;
    private const COUNTER_ID = RequestArgumentsEnum::COUNTER_ID;
    private const VALUE      = RequestArgumentsEnum::VALUE;
    private const DATE       = RequestArgumentsEnum::DATE;
    private const FILE       = RequestArgumentsEnum::FILE;

    public function rules(): array
    {
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

    public function getId(): ?int
    {
        return $this->getIntOrNull(self::ID);
    }

    public function getCounterId(): int
    {
        return $this->getInt(self::COUNTER_ID);
    }

    public function getValue(): int
    {
        return $this->getInt(self::VALUE);
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file(self::FILE);
    }

    public function getDate(): Carbon
    {
        return $this->getStringOrNull(self::DATE) ?Carbon::parse($this->getStringOrNull(self::DATE)) : Carbon::now();
    }
}
