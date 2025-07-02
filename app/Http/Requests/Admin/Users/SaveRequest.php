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
    private const ID          = RequestArgumentsEnum::ID;
    private const LAST_NAME   = RequestArgumentsEnum::LAST_NAME;
    private const FIRST_NAME  = RequestArgumentsEnum::FIRST_NAME;
    private const MIDDLE_NAME = RequestArgumentsEnum::MIDDLE_NAME;
    private const EMAIL       = RequestArgumentsEnum::EMAIL;
    private const PHONE       = RequestArgumentsEnum::PHONE;
    private const IS_MEMBER   = RequestArgumentsEnum::IS_MEMBER;
    private const ROLE        = 'role_id';
    private const ACCOUNT     = 'account_id';

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

    public function getAccountId(): int
    {
        return $this->getInt(self::ACCOUNT);
    }

    public function getPhone(): ?string
    {
        return $this->getStringOrNull(self::PHONE);
    }

    public function getOwnershipDate(): ?Carbon
    {
        return $this->getDateOrNull('ownershipDate');
    }

    public function getOwnershipDutyInfo(): ?string
    {
        return $this->getStringOrNull('ownershipDutyInfo');
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
}
