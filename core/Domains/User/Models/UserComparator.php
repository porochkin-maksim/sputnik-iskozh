<?php declare(strict_types=1);

namespace Core\Domains\User\Models;

use App\Models\User;
use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;
use Core\Enums\DateTimeFormat;

class UserComparator extends AbstractComparatorDTO
{
    public const  string TITLE_EMAIL               = 'Почта';
    public const  string TITLE_PHONE               = 'Телефон';
    public const  string TITLE_FIRST_NAME          = 'Имя';
    public const  string TITLE_MIDDLE_NAME         = 'Отчество';
    public const  string TITLE_LAST_NAME           = 'Фамилия';
    public const  string TITLE_TELEGRAM_ID         = 'ID Телеграма';
    public const  string TITLE_ACCOUNT             = 'Участок';
    public const  string TITLE_ROLE                = 'Роль';
    private const string TITLE_OWNERSHIP_DATE      = 'Дата членства';
    private const string TITLE_OWNERSHIP_DUTY_INFO = 'Основание членства';

    private const string KEY_ACCOUNT             = 'account';
    private const string KEY_ROLE                = 'role';
    private const string KEY_OWNERSHIP_DATE             = 'ownershipDate';
    private const string KEY_OWNERSHIP_DUTY_INFO = 'ownershipDutyInfo';

    protected const array KEYS_TO_TITLES = [
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
        $account = $entity->getAccount();
        $role    = $entity->getRole(true);

        $this->initProperties($entity, $entity->getId());

        $this->addProperties([
            self::KEY_ACCOUNT             => $account?->getId(),
            self::KEY_ROLE                => $role?->getId(),
            self::KEY_OWNERSHIP_DATE      => $entity->getOwnershipDate()?->format(DateTimeFormat::DATE_DEFAULT),
            self::KEY_OWNERSHIP_DUTY_INFO => $entity->getOwnershipDutyInfo(),
        ]);

        $this->expandedProperties = [
            self::KEY_ACCOUNT => $account ? sprintf('id: %s, номер: %s', $account?->getId(), $account?->getNumber()) : null,
            self::KEY_ROLE    => $role ? sprintf('id: %s, название: %s', $role->getId(), $role->getName()) : null,
        ];
    }
}
