<?php declare(strict_types=1);

namespace Core\Domains\User\Models;

use App\Models\User;
use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;

class UserComparator extends AbstractComparatorDTO
{
    public const TITLE_EMAIL       = 'Почта';
    public const TITLE_PHONE       = 'Телефон';
    public const TITLE_FIRST_NAME  = 'Имя';
    public const TITLE_MIDDLE_NAME = 'Отчество';
    public const TITLE_LAST_NAME   = 'Фамилия';
    public const TITLE_TELEGRAM_ID = 'ID Телеграма';
    public const TITLE_ACCOUNT     = 'Участок';
    public const TITLE_ROLE        = 'Роль';

    private const KEY_ACCOUNT = 'account';
    private const KEY_ROLE    = 'role';

    protected const KEYS_TO_TITLES = [
        User::EMAIL       => self::TITLE_EMAIL,
        User::PHONE       => self::TITLE_PHONE,
        User::FIRST_NAME  => self::TITLE_FIRST_NAME,
        User::MIDDLE_NAME => self::TITLE_MIDDLE_NAME,
        User::LAST_NAME   => self::TITLE_LAST_NAME,
        User::TELEGRAM_ID => self::TITLE_TELEGRAM_ID,
        self::KEY_ACCOUNT => self::TITLE_ACCOUNT,
        self::KEY_ROLE    => self::TITLE_ROLE,
    ];

    public function __construct(UserDTO $entity)
    {
        $this->initProperties($entity, $entity->getId());

        $this->expandedProperties = [
            self::KEY_ACCOUNT => $entity->getAccount() ? sprintf('id: %s, номер: %s', $entity->getAccount()->getId(), $entity->getAccount()->getNumber()) : null,
            self::KEY_ROLE    => $entity->getRole() ? sprintf('id: %s, название: %s', $entity->getRole()->getId(), $entity->getRole()->getName()) : null,
        ];
    }
}
