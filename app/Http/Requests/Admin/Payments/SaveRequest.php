<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Payments;

use App\Http\Requests\AbstractRequest;
use App\Models\Billing\Payment;
use Carbon\Carbon;

class SaveRequest extends AbstractRequest
{
    private const string ID      = 'id';
    private const string NAME    = 'name';
    private const string COST    = 'cost';
    private const string COMMENT = 'comment';
    private const string PAID    = 'paidAt';

    public function rules(): array
    {
        return [
            self::COST    => [
                'required',
                'numeric',
                'min:0',
            ],
            self::NAME    => [
                'nullable',
                'string',
            ],
            self::COMMENT => [
                'nullable',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::COST . '.required' => sprintf('Укажите «%s»', Payment::TITLE_COST),
            self::COST . '.numeric'  => sprintf('«%s» должна быть числом', Payment::TITLE_COST),
            self::COST . '.min'      => sprintf('«%s» должна быть больше :min', Payment::TITLE_COST),

            self::COMMENT . '.string' => sprintf('Неверное значение «%s»', Payment::TITLE_COMMENT),
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

    public function getPaidAt(): ?Carbon
    {
        return $this->getDateOrNull(self::PAID);
    }
}
