<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Services;

use App\Http\Requests\AbstractRequest;
use App\Models\Billing\Period;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Service\Models\ServiceComparator;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Validation\Rule;

class SaveRequest extends AbstractRequest
{
    private const ID        = RequestArgumentsEnum::ID;
    private const NAME      = RequestArgumentsEnum::NAME;
    private const TYPE      = RequestArgumentsEnum::TYPE;
    private const COST      = RequestArgumentsEnum::COST;
    private const ACTIVE    = RequestArgumentsEnum::ACTIVE;
    private const PERIOD_ID = RequestArgumentsEnum::PERIOD_ID;

    public function rules(): array
    {
        return [
            self::NAME      => [
                'required',
                'string',
                'max:255',
            ],
            self::TYPE      => [
                'required',
                'in:' . implode(',', ServiceTypeEnum::values()),
            ],
            self::PERIOD_ID => [
                'required',
                Rule::exists(Period::TABLE, Period::ID),
            ],
            self::COST      => [
                'required',
                'numeric',
                'min:0',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::NAME . '.required' => sprintf('Укажите «%s»', ServiceComparator::TITLE_NAME),
            self::NAME . '.string'   => sprintf('Укажите «%s»', ServiceComparator::TITLE_NAME),
            self::NAME . '.max'      => sprintf('«%s» слишком длинное', ServiceComparator::TITLE_NAME),

            self::TYPE . '.required' => sprintf('Укажите «%s»', ServiceComparator::TITLE_TYPE),
            self::TYPE . '.in'       => sprintf('Неверный «%s»', ServiceComparator::TITLE_TYPE),

            self::PERIOD_ID . '.required' => sprintf('Укажите «%s»', ServiceComparator::TITLE_PERIOD_ID),
            self::PERIOD_ID . '.exists'   => sprintf('Указанный «%s» не существует', ServiceComparator::TITLE_PERIOD_ID),

            self::COST . '.required' => sprintf('Укажите «%s»', ServiceComparator::TITLE_COST),
            self::COST . '.numeric'  => sprintf('«%s» должна быть числом', ServiceComparator::TITLE_COST),
            self::COST . '.min'      => sprintf('«%s» должна быть больше :min', ServiceComparator::TITLE_COST),
        ];
    }

    public function getId(): int
    {
        return $this->getInt(self::ID);
    }

    public function getName(): string
    {
        return $this->get(self::NAME);
    }

    public function getCost(): float
    {
        return $this->getFloat(self::COST);
    }

    public function getType(): ?int
    {
        return $this->getIntOrNull(self::TYPE);
    }

    public function getIsActive(): bool
    {
        return $this->getBool(self::ACTIVE);
    }

    public function getPeriodId(): int
    {
        return $this->getInt(self::PERIOD_ID);
    }
}
