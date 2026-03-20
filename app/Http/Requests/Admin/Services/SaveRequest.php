<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Services;

use App\Http\Requests\AbstractRequest;
use App\Models\Billing\Period;
use App\Models\Billing\Service;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Illuminate\Validation\Rule;

class SaveRequest extends AbstractRequest
{
    private const string ID        = 'id';
    private const string NAME      = 'name';
    private const string TYPE      = 'type';
    private const string COST      = 'cost';
    private const string ACTIVE    = 'active';
    private const string PERIOD_ID = 'period_id';

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
            self::NAME . '.required' => sprintf('Укажите «%s»', Service::TITLE_NAME),
            self::NAME . '.string'   => sprintf('Укажите «%s»', Service::TITLE_NAME),
            self::NAME . '.max'      => sprintf('«%s» слишком длинное', Service::TITLE_NAME),

            self::TYPE . '.required' => sprintf('Укажите «%s»', Service::TITLE_TYPE),
            self::TYPE . '.in'       => sprintf('Неверный «%s»', Service::TITLE_TYPE),

            self::PERIOD_ID . '.required' => sprintf('Укажите «%s»', Service::TITLE_PERIOD_ID),
            self::PERIOD_ID . '.exists'   => sprintf('Указанный «%s» не существует', Service::TITLE_PERIOD_ID),

            self::COST . '.required' => sprintf('Укажите «%s»', Service::TITLE_COST),
            self::COST . '.numeric'  => sprintf('«%s» должна быть числом', Service::TITLE_COST),
            self::COST . '.min'      => sprintf('«%s» должна быть больше :min', Service::TITLE_COST),
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
