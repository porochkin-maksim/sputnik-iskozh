<?php declare(strict_types=1);

namespace Core\Domains\User\Models;

use App\Models\User;
use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;
use Core\Enums\DateTimeFormat;

class UserComparator extends AbstractComparatorDTO
{
    public const  TITLE_EMAIL               = 'Почта';
    public const  TITLE_PHONE               = 'Телефон';
    public const  TITLE_FIRST_NAME          = 'Имя';
    public const  TITLE_MIDDLE_NAME         = 'Отчество';
    public const  TITLE_LAST_NAME           = 'Фамилия';
    public const  TITLE_TELEGRAM_ID         = 'ID Телеграма';
    public const  TITLE_ACCOUNT             = 'Участок';
    public const  TITLE_ROLE                = 'Роль';
    private const TITLE_OWNERSHIP_DATE      = 'Дата членства';
    private const TITLE_OWNERSHIP_DUTY_INFO = 'Основание членства';

    private const KEY_ACCOUNT             = 'account';
    private const KEY_ROLE                = 'role';
    private const KEY_OWNERSHIP_DATE      = 'ownershipDate';
    private const KEY_OWNERSHIP_DUTY_INFO = 'ownershipDutyInfo';

    protected const KEYS_TO_TITLES = [
        User::EMAIL                   => self::TITLE_EMAIL,
        User::PHONE                   => self::TITLE_PHONE,
        User::FIRST_NAME              => self::TITLE_FIRST_NAME,
        User::MIDDLE_NAME             => self::TITLE_MIDDLE_NAME,
        User::LAST_NAME               => self::TITLE_LAST_NAME,
        User::TELEGRAM_ID             => self::TITLE_TELEGRAM_ID,
        self::KEY_ACCOUNT             => self::TITLE_ACCOUNT,
        self::KEY_ROLE                => self::TITLE_ROLE,
        self::KEY_OWNERSHIP_DATE      => self::TITLE_OWNERSHIP_DATE,
        self::KEY_OWNERSHIP_DUTY_INFO => self::TITLE_OWNERSHIP_DUTY_INFO,
    ];

    public function __construct(UserDTO $entity)
    {
        $account = $entity->getAccount(true);
        $role    = $entity->getRole(true);
        $exData  = $entity->getExData();

        $this->initProperties($entity, $entity->getId());

        $this->addProperties([
            self::KEY_ACCOUNT             => $account?->getId(),
            self::KEY_ROLE                => $role?->getId(),
            self::KEY_OWNERSHIP_DATE      => $exData->getOwnershipDate()?->format(DateTimeFormat::DATE_DEFAULT),
            self::KEY_OWNERSHIP_DUTY_INFO => $exData->getOwnershipDutyInfo(),
        ]);

        $this->expandedProperties = [
            self::KEY_ACCOUNT => $account ? sprintf('id: %s, номер: %s', $account->getId(), $account->getNumber()) : null,
            self::KEY_ROLE    => $role ? sprintf('id: %s, название: %s', $role->getId(), $role->getName()) : null,
        ];
    }
}
