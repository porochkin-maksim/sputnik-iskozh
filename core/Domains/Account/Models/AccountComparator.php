<?php declare(strict_types=1);

namespace Core\Domains\Account\Models;

use App\Models\Account\Account;
use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;

class AccountComparator extends AbstractComparatorDTO
{
    public const TITLE_NUMBER      = 'Номер';
    public const TITLE_SIZE        = 'Площадь';
    public const TITLE_BALANCE     = 'Баланс';
    public const TITLE_IS_VERIFIED = 'Подтверждён';
    public const TITLE_IS_MEMBER   = 'Член СНТ';

    protected const KEYS_TO_TITLES = [
        Account::NUMBER      => self::TITLE_NUMBER,
        Account::SIZE        => self::TITLE_SIZE,
        Account::BALANCE     => self::TITLE_BALANCE,
        Account::IS_VERIFIED => self::TITLE_IS_VERIFIED,
        Account::IS_MEMBER   => self::TITLE_IS_MEMBER,
    ];

    public function __construct(AccountDTO $entity)
    {
        $this->initProperties($entity, $entity->getId());

        $this->expandedProperties = [
            Account::IS_VERIFIED => $this->getYesNoText($entity->isVerified()),
            Account::IS_MEMBER   => $this->getYesNoText($entity->isMember()),
        ];
    }
}
