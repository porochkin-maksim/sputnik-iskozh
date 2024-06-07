<?php declare(strict_types=1);

namespace Core\Objects\Report\Requests;

use App\Http\Requests\AbstractRequest;
use Carbon\Carbon;
use Core\Enums\DateTimeFormat;
use Core\Objects\Report\Enums\CategoryEnum;
use Core\Objects\Report\Enums\TypeEnum;
use Core\Objects\Report\Models\ReportDTO;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

class SaveRequest extends AbstractRequest
{
    private const ID       = RequestArgumentsEnum::ID;
    private const NAME     = RequestArgumentsEnum::NAME;
    private const YEAR     = RequestArgumentsEnum::YEAR;
    private const CATEGORY = RequestArgumentsEnum::CATEGORY;
    private const TYPE     = RequestArgumentsEnum::TYPE;
    private const START_AT = RequestArgumentsEnum::START_AT;
    private const END_AT   = RequestArgumentsEnum::END_AT;
    private const MONEY   = RequestArgumentsEnum::MONEY;

    public function rules(): array
    {
        return [
            self::NAME     => [
                'nullable',
                'string',
                'min:3',
            ],
            self::YEAR     => [
                'integer',
            ],
            self::CATEGORY => [
                Rule::in(CategoryEnum::values()),
            ],
            self::TYPE     => [
                Rule::in(TypeEnum::values()),
            ],
            self::START_AT => [
                'sometimes',
                'date_format:' . DateTimeFormat::DATE_DEFAULT,
            ],
            self::END_AT   => [
                'sometimes',
                'date_format:' . DateTimeFormat::DATE_DEFAULT,
            ],
            self::MONEY   => [
                'nullable',
                'numeric',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::YEAR . '.required'     => '',
            self::CATEGORY . '.required' => '',
            self::TYPE . '.required'     => '',
            self::START_AT . '.required' => '',
            self::END_AT . '.required'   => '',
        ];
    }

    public function dto(): ReportDTO
    {
        $dto = new ReportDTO();
        $dto->setId($this->getInt(self::ID))
            ->setName($this->get(self::NAME))
            ->setYear($this->getInt(self::YEAR))
            ->setCategory(CategoryEnum::from($this->getInt(self::CATEGORY)))
            ->setType(TypeEnum::from($this->getInt(self::TYPE)))
            ->setMoney($this->getFloat(self::MONEY))
            ->setStartAt($this->get(self::START_AT) ? Carbon::parse($this->get(self::START_AT)) : null)
            ->setEndAt($this->get(self::END_AT) ? Carbon::parse($this->get(self::END_AT)) : null);

        return $dto;
    }
}
