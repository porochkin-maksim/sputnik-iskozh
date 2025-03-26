<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Payments;

use App\Http\Requests\AbstractRequest;
use Core\Domains\Billing\Payment\Models\PaymentComparator;
use Core\Requests\RequestArgumentsEnum;

class SaveRequest extends AbstractRequest
{
    private const ID         = RequestArgumentsEnum::ID;
    private const NAME       = RequestArgumentsEnum::NAME;
    private const COST       = RequestArgumentsEnum::COST;
    private const COMMENT    = RequestArgumentsEnum::COMMENT;

    public function rules(): array
    {
        return [
            self::COST       => [
                'required',
                'numeric',
                'min:0',
            ],
            self::NAME    => [
                'nullable',
                'string',
            ],
            self::COMMENT    => [
                'nullable',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::COST . '.required' => sprintf('Укажите «%s»', PaymentComparator::TITLE_COST),
            self::COST . '.numeric'  => sprintf('«%s» должна быть числом', PaymentComparator::TITLE_COST),
            self::COST . '.min'      => sprintf('«%s» должна быть больше :min', PaymentComparator::TITLE_COST),

            self::COMMENT . '.string' => sprintf('Неверное значение «%s»', PaymentComparator::TITLE_COMMENT),
        ];
    }

    public function getId(): ?int
    {
        return $this->getIntOrNull(self::ID);
    }

    public function getCost(): float
    {
        return $this->getFloat(self::COST);
    }

    public function getName(): ?string
    {
        return $this->getStringOrNull(self::NAME);
    }

    public function getComment(): ?string
    {
        return $this->getStringOrNull(self::COMMENT);
    }
}
