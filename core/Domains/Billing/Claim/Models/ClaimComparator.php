<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim\Models;

use App\Models\Billing\Claim;
use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;

class ClaimComparator extends AbstractComparatorDTO
{
    public const string TITLE_INVOICE_ID = 'Счёт';
    public const string TITLE_SERVICE_ID = 'Услуга';
    public const string TITLE_TARIFF     = 'Тариф';
    public const string TITLE_COST       = 'Стоимость';
    public const string TITLE_PAID       = 'Оплачено';

    protected const array KEYS_TO_TITLES = [
        Claim::INVOICE_ID => self::TITLE_INVOICE_ID,
        Claim::SERVICE_ID => self::TITLE_SERVICE_ID,
        Claim::TARIFF     => self::TITLE_TARIFF,
        Claim::COST       => self::TITLE_COST,
        Claim::PAID       => self::TITLE_PAID,
    ];

    public function __construct(ClaimDTO $entity)
    {
        $this->initProperties($entity, $entity->getId());
    }
}
