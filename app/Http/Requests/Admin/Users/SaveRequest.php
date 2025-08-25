<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Users;

use App\Http\Requests\AbstractRequest;
use App\Models\User;
use Carbon\Carbon;
use Core\Helpers\Phone\PhoneHelper;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Validation\Rule;

class SaveRequest extends AbstractRequest
{
    private const string ID          = RequestArgumentsEnum::ID;
    private const string LAST_NAME   = RequestArgumentsEnum::LAST_NAME;
    private const string FIRST_NAME  = RequestArgumentsEnum::FIRST_NAME;
    private const string MIDDLE_NAME = RequestArgumentsEnum::MIDDLE_NAME;
    private const string EMAIL       = RequestArgumentsEnum::EMAIL;
    private const string PHONE       = RequestArgumentsEnum::PHONE;
    private const string ROLE        = 'role_id';

    public function rules(): array
    {
        return [
            self::EMAIL => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::TABLE, User::EMAIL)->ignore($this->get(self::ID)),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::EMAIL . '.required' => 'Заполните поле "эл.почта"',
            self::EMAIL . '.unique'   => 'Адрес уже занят',
            self::EMAIL . '.string'   => 'Поле должно быть строкой',
            self::EMAIL . '.email'    => 'Поле должно быть корретным адресом эл.почты',
            self::EMAIL . '.max'      => 'Количество символов должно быть меньше :max',
        ];
    }

    public function getId(): int
    {
        return $this->getInt(self::ID);
    }

    public function getLastName(): ?string
    {
        return $this->getStringOrNull(self::LAST_NAME);
    }

    public function getFirstName(): ?string
    {
        return $this->getStringOrNull(self::FIRST_NAME);
    }

    public function getMiddleName(): ?string
    {
        return $this->getStringOrNull(self::MIDDLE_NAME);
    }

    public function getEmail(): string
    {
        return $this->getStringOrNull(self::EMAIL);
    }

    public function getRoleId(): int
    {
        return $this->getInt(self::ROLE);
    }

    public function getFractions(): array
    {
        return $this->getArray('fractions');
    }

    public function getPhone(): ?string
    {
        return $this->getStringOrNull(self::PHONE);
    }

    public function getMembershipDate(): ?Carbon
    {
        return $this->getDateOrNull('membershipDate');
    }

    public function getMembershipDutyInfo(): ?string
    {
        return $this->getStringOrNull('membershipDutyInfo');
    }

    public function getAddPhone(): ?string
    {
        return PhoneHelper::normalizePhone($this->getStringOrNull('add_phone'));
    }

    public function getLegalAddress(): ?string
    {
        return $this->getStringOrNull('legal_address');
    }

    public function getPostAddress(): ?string
    {
        return $this->getStringOrNull('post_address') ?: $this->getLegalAddress();
    }

    public function getAdditional(): ?string
    {
        return $this->getStringOrNull('additional');
    }
}
