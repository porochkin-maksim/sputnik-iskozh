<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim\Models;

use App\Models\Billing\Claim;
use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;

class ClaimComparator extends AbstractComparatorDTO
{
    public const TITLE_INVOICE_ID = 'Счёт';
    public const TITLE_SERVICE_ID = 'Услуга';
    public const TITLE_TARIFF     = 'Тариф';
    public const TITLE_COST       = 'Стоимость';
    public const TITLE_PAYED      = 'Оплачено';

    protected const KEYS_TO_TITLES = [
        Claim::INVOICE_ID => self::TITLE_INVOICE_ID,
        Claim::SERVICE_ID => self::TITLE_SERVICE_ID,
        Claim::TARIFF     => self::TITLE_TARIFF,
        Claim::COST       => self::TITLE_COST,
        Claim::PAYED      => self::TITLE_PAYED,
    ];

    public function __construct(ClaimDTO $entity)
    {
        $this->initProperties($entity, $entity->getId());

        $this->expandedProperties = [
            // Claim::PERIOD_ID => $entity->getPeriodId(),
            // Claim::ACCOUNT_ID   => $entity->getAccountId(),
        ];
    }
}
