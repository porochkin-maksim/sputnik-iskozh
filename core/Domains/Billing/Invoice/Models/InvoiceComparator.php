<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Models;

use App\Models\Billing\Invoice;
use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;

class InvoiceComparator extends AbstractComparatorDTO
{
    public const string TITLE_PERIOD_ID  = 'Период';
    public const string TITLE_ACCOUNT_ID = 'Участок';
    public const string TITLE_TYPE       = 'Тип';
    public const string TITLE_PAYED      = 'Оплачено';
    public const string TITLE_COST       = 'Стоимость';
    public const string TITLE_COMMENT    = 'Комментарий';
    public const string TITLE_NAME       = 'Название';

    protected const array KEYS_TO_TITLES = [
        Invoice::PERIOD_ID  => self::TITLE_PERIOD_ID,
        Invoice::ACCOUNT_ID => self::TITLE_ACCOUNT_ID,
        Invoice::NAME       => self::TITLE_NAME,
        Invoice::TYPE       => self::TITLE_TYPE,
        Invoice::PAYED      => self::TITLE_PAYED,
        Invoice::COST       => self::TITLE_COST,
        Invoice::COMMENT    => self::TITLE_COMMENT,
    ];

    public function __construct(InvoiceDTO $entity)
    {
        $this->initProperties($entity, $entity->getId());
    }
}
