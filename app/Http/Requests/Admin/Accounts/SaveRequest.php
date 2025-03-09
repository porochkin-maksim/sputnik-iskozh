<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Accounts;

use App\Http\Requests\AbstractRequest;
use App\Models\Account\Account;
use Core\Domains\Account\Models\AccountComparator;
use Core\Domains\Enums\Regexp;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Validation\Rule;

class SaveRequest extends AbstractRequest
{
    private const ACCOUNT_REGEXP = Regexp::ACCOUNT_NAME;

    private const ID        = RequestArgumentsEnum::ID;
    private const NUMBER    = RequestArgumentsEnum::NUMBER;
    private const SIZE      = RequestArgumentsEnum::SIZE;
    private const IS_MEMBER = RequestArgumentsEnum::IS_MEMBER;

    public function rules(): array
    {
        return [
            self::NUMBER    => [
                'required',
                'string',
                'regex:' . self::ACCOUNT_REGEXP,
                Rule::unique(Account::TABLE, Account::NUMBER)->ignore($this->get(self::ID)),
            ],
            self::SIZE      => [
                'required',
                'numeric',
                'min:0',
            ],
            self::IS_MEMBER => [
                'required',
                'in:true,false,1,0',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::NUMBER . '.required' => sprintf('Укажите «%s»', AccountComparator::TITLE_NUMBER),
            self::NUMBER . '.string'   => sprintf('Укажите «%s»', AccountComparator::TITLE_NUMBER),
            self::NUMBER . '.regex'    => sprintf('Неверный формат для «%s»', AccountComparator::TITLE_NUMBER),
            self::NUMBER . '.unique'   => sprintf('Значение для «%s» уже существует', AccountComparator::TITLE_NUMBER),

            self::SIZE . '.required' => sprintf('Укажите «%s»', AccountComparator::TITLE_SIZE),
            self::SIZE . '.numeric'  => sprintf('Укажите число для «%s»', AccountComparator::TITLE_SIZE),
            self::SIZE . '.min'      => sprintf('Минимальное значение для «%s» - :min', AccountComparator::TITLE_SIZE),

            self::IS_MEMBER . '.required' => sprintf('Укажите «%s»', AccountComparator::TITLE_IS_MEMBER),
            self::IS_MEMBER . '.in'     => sprintf('Некорректное значение «%s»', AccountComparator::TITLE_IS_MEMBER),
        ];
    }

    public function getId(): ?int
    {
        return $this->getIntOrNull(self::ID);
    }

    public function getNumber(): ?string
    {
        return $this->getStringOrNull(self::NUMBER);
    }

    public function getSize(): ?int
    {
        return $this->getIntOrNull(self::SIZE);
    }

    public function getIsMember(): bool
    {
        return $this->getBool(self::IS_MEMBER);
    }
}
