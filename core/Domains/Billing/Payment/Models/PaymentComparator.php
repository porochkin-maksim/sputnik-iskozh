<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment\Models;

use App\Models\Billing\Payment;
use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;
use Core\Enums\DateTimeFormat;

class PaymentComparator extends AbstractComparatorDTO
{
    public const string TITLE_INVOICE_ID = 'Счёт';
    public const string TITLE_ACCOUNT_ID = 'Участок';
    public const string TITLE_COST       = 'Стоимость';
    public const string TITLE_MODERATED  = 'Модерирован';
    public const string TITLE_VERIFIED   = 'Подтверждён';
    public const string TITLE_COMMENT    = 'Комментарий';
    public const string TITLE_NAME       = 'Название';

    protected const array KEYS_TO_TITLES = [
        Payment::INVOICE_ID => self::TITLE_INVOICE_ID,
        Payment::ACCOUNT_ID => self::TITLE_ACCOUNT_ID,
        Payment::COST       => self::TITLE_COST,
        Payment::MODERATED  => self::TITLE_MODERATED,
        Payment::VERIFIED   => self::TITLE_VERIFIED,
        Payment::NAME       => self::TITLE_NAME,
        Payment::COMMENT    => self::TITLE_COMMENT,
    ];

    public function __construct(PaymentDTO $entity)
    {
        $this->initProperties($entity, $entity->getId());

        $this->expandedProperties = [
            Payment::PAYED_AT => $entity->getPayedAt()?->format(DateTimeFormat::DATE_VIEW_FORMAT),
        ];
    }
}
