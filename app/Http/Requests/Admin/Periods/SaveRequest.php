<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Periods;

use App\Http\Requests\AbstractRequest;
use Carbon\Carbon;
use Core\Domains\Billing\Period\Models\PeriodComparator;
use Core\Helpers\DateTime\DateTimeHelper;
use Core\Requests\RequestArgumentsEnum;

class SaveRequest extends AbstractRequest
{
    private const ID        = RequestArgumentsEnum::ID;
    private const NAME      = RequestArgumentsEnum::NAME;
    private const START_AT  = RequestArgumentsEnum::START_AT;
    private const END_AT    = RequestArgumentsEnum::END_AT;
    private const IS_CLOSED = 'is_closed';

    public function rules(): array
    {
        return [
            self::NAME      => [
                'required',
                'string',
                'max:255',
            ],
            self::START_AT  => [
                'required',
                'date',
            ],
            self::END_AT    => [
                'required',
                'date',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::NAME . '.required' => sprintf('Укажите «%s»', PeriodComparator::TITLE_NAME),
            self::NAME . '.string'   => sprintf('Укажите «%s»', PeriodComparator::TITLE_NAME),
            self::NAME . '.max'      => sprintf('«%s» слишком длинное', PeriodComparator::TITLE_NAME),

            self::START_AT . '.required' => sprintf('Укажите «%s»', PeriodComparator::TITLE_START_AT),
            self::START_AT . '.date'     => sprintf('Укажите «%s»', PeriodComparator::TITLE_START_AT),

            self::END_AT . '.required' => sprintf('Укажите «%s»', PeriodComparator::TITLE_END_AT),
            self::END_AT . '.date'     => sprintf('Укажите «%s»', PeriodComparator::TITLE_END_AT),
        ];
    }

    public function getId(): int
    {
        return $this->getInt(self::ID);
    }

    public function getName(): ?string
    {
        return $this->getStringOrNull(self::NAME);
    }

    public function getStartAt(): ?Carbon
    {
        return DateTimeHelper::toCarbonOrNull($this->get(self::START_AT));
    }

    public function getEndAt(): ?Carbon
    {
        return DateTimeHelper::toCarbonOrNull($this->get(self::END_AT));
    }

    public function getIsClosed(): bool
    {
        return $this->getBool(self::IS_CLOSED);
    }
}
