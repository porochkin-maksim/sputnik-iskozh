<?php declare(strict_types=1);

namespace Core\Domains\Counter\Models;

use App\Models\Counter\Counter;
use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;

class CounterComparator extends AbstractComparatorDTO
{
    public const TITLE_TYPE         = 'Тип';
    public const TITLE_ACCOUNT_ID   = 'Участок';
    public const TITLE_NUMBER       = 'Номер';
    public const TITLE_IS_INVOICING = 'Выставлять счета';
    public const TITLE_INCREMENT    = 'Автоприращение (кВт)';

    protected const KEYS_TO_TITLES = [
        Counter::TYPE         => self::TITLE_TYPE,
        Counter::ACCOUNT_ID   => self::TITLE_ACCOUNT_ID,
        Counter::NUMBER       => self::TITLE_NUMBER,
        Counter::IS_INVOICING => self::TITLE_IS_INVOICING,
        Counter::INCREMENT    => self::TITLE_INCREMENT,
    ];

    public function __construct(CounterDTO $entity)
    {
        $this->initProperties($entity, $entity->getId());

        $this->expandedProperties = [
            Counter::TYPE         => $entity->getType()?->name(),
            Counter::ACCOUNT_ID   => $entity->getAccountId(),
            Counter::NUMBER       => $entity->getNumber(),
            Counter::IS_INVOICING => $this->getYesNoText($entity->isInvoicing()),
            Counter::INCREMENT    => $entity->getIncrement(),
        ];
    }
}
