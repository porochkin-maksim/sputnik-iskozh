<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Models;

use App\Models\Billing\Invoice;
use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;

class InvoiceComparator extends AbstractComparatorDTO
{
    public const TITLE_PERIOD_ID  = 'Период';
    public const TITLE_ACCOUNT_ID = 'Участок';
    public const TITLE_TYPE       = 'Тип';
    public const TITLE_PAYED      = 'Оплачено';
    public const TITLE_COST       = 'Стоимость';
    public const TITLE_COMMENT    = 'Комментарий';

    protected const KEYS_TO_TITLES = [
        Invoice::PERIOD_ID  => self::TITLE_PERIOD_ID,
        Invoice::ACCOUNT_ID => self::TITLE_ACCOUNT_ID,
        Invoice::TYPE       => self::TITLE_TYPE,
        Invoice::PAYED      => self::TITLE_PAYED,
        Invoice::COST       => self::TITLE_COST,
        Invoice::COMMENT    => self::TITLE_COMMENT,
    ];

    public function __construct(InvoiceDTO $entity)
    {
        $this->initProperties($entity, $entity->getId());

        $this->expandedProperties = [
            // Invoice::PERIOD_ID => $entity->getPeriodId(),
            // Invoice::ACCOUNT_ID   => $entity->getAccountId(),
        ];
    }
}
