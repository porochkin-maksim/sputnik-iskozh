<?php declare(strict_types=1);

namespace Core\Domains\Billing\Transaction\Models;

use App\Models\Billing\Transaction;
use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;

class TransactionComparator extends AbstractComparatorDTO
{
    public const TITLE_INVOICE_ID = 'Счёт';
    public const TITLE_SERVICE_ID = 'Услуга';
    public const TITLE_TARIFF     = 'Тариф';
    public const TITLE_COST       = 'Стоимость';
    public const TITLE_PAYED      = 'Оплачено';

    protected const KEYS_TO_TITLES = [
        Transaction::INVOICE_ID => self::TITLE_INVOICE_ID,
        Transaction::SERVICE_ID => self::TITLE_SERVICE_ID,
        Transaction::TARIFF     => self::TITLE_TARIFF,
        Transaction::COST       => self::TITLE_COST,
        Transaction::PAYED      => self::TITLE_PAYED,
    ];

    public function __construct(TransactionDTO $entity)
    {
        $this->initProperties($entity, $entity->getId());

        $this->expandedProperties = [
            // Transaction::PERIOD_ID => $entity->getPeriodId(),
            // Transaction::ACCOUNT_ID   => $entity->getAccountId(),
        ];
    }
}
