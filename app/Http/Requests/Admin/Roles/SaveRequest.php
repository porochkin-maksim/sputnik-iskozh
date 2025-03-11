<?php declare(strict_types=1);

namespace App\Http\Requests\Admin\Roles;

use App\Http\Requests\AbstractRequest;
use App\Models\Access\Role;
use Core\Domains\Access\Models\RoleComparator;
use Core\Domains\Enums\Regexp;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Validation\Rule;

class SaveRequest extends AbstractRequest
{
    private const ACCOUNT_REGEXP = Regexp::ACCOUNT_NAME;

    private const ID          = RequestArgumentsEnum::ID;
    private const NAME        = RequestArgumentsEnum::NAME;
    private const PERMISSIONS = RequestArgumentsEnum::PERMISSIONS;

    public function rules(): array
    {
        return [
            self::NAME      => [
                'required',
                'string',
                Rule::unique(Role::TABLE, Role::NAME)->ignore($this->get(self::ID)),
            ],
            self::PERMISSIONS => [
                'required',
                'array',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            self::NAME . '.required' => sprintf('Укажите «%s»', RoleComparator::TITLE_NAME),
            self::NAME . '.string'   => sprintf('Укажите «%s»', RoleComparator::TITLE_NAME),
            self::NAME . '.unique'   => sprintf('Значение для «%s» уже существует', RoleComparator::TITLE_NAME),

            self::PERMISSIONS . '.required' => sprintf('Укажите «%s»', RoleComparator::TITLE_PERMISSIONS),
            self::PERMISSIONS . '.array'   => sprintf('Укажите «%s»', RoleComparator::TITLE_PERMISSIONS),
        ];
    }

    public function getId(): ?int
    {
        return $this->getIntOrNull(self::ID);
    }

    public function getName(): ?string
    {
        return $this->getStringOrNull(self::NAME);
    }

    public function getPersmissions(): ?array
    {
        return array_unique($this->getArray(self::PERMISSIONS));
    }
}
