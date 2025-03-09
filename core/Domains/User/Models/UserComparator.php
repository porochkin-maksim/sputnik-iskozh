<?php declare(strict_types=1);

namespace Core\Domains\User\Models;

use App\Models\User;
use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;
use Core\Enums\DateTimeFormat;

class UserComparator extends AbstractComparatorDTO
{
    public const TITLE_EMAIL             = 'Почта';
    public const TITLE_FIRST_NAME        = 'Имя';
    public const TITLE_MIDDLE_NAME       = 'Отчество';
    public const TITLE_LAST_NAME         = 'Фамилия';
    // public const TITLE_EMAIL_VERIFIED_AT = 'Почта подтверждена';
    public const TITLE_TELEGRAM_ID       = 'ID Телеграма';

    protected const KEYS_TO_TITLES = [
        User::EMAIL             => self::TITLE_EMAIL,
        User::FIRST_NAME        => self::TITLE_FIRST_NAME,
        User::MIDDLE_NAME       => self::TITLE_MIDDLE_NAME,
        User::LAST_NAME         => self::TITLE_LAST_NAME,
        // User::EMAIL_VERIFIED_AT => self::TITLE_EMAIL_VERIFIED_AT,
        User::TELEGRAM_ID       => self::TITLE_TELEGRAM_ID,
    ];

    public function __construct(UserDTO $entity)
    {
        $this->initProperties($entity, $entity->getId());

        $this->expandedProperties = [
            // User::EMAIL_VERIFIED_AT => $entity->em()?->format(DateTimeFormat::DATE_TIME_VIEW_FULL),
        ];
    }
}
