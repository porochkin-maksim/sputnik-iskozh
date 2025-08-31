<?php declare(strict_types=1);

namespace Core\Domains\Account\Models;

use App\Models\Account\Account;
use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;
use Core\Enums\DateTimeFormat;

class AccountComparator extends AbstractComparatorDTO
{
    public const TITLE_NUMBER          = 'Номер';
    public const TITLE_SIZE            = 'Площадь';
    public const TITLE_IS_VERIFIED     = 'Подтверждён';
    public const TITLE_IS_INVOICING    = 'Выставление счетов';
    public const TITLE_CADASTRE_NUMBER = 'Кадастровый номер';

    private const KEY_CADASTRE_NUMBER = 'cadastreNumber';

    protected const KEYS_TO_TITLES = [
        Account::NUMBER           => self::TITLE_NUMBER,
        Account::SIZE             => self::TITLE_SIZE,
        Account::IS_VERIFIED      => self::TITLE_IS_VERIFIED,
        Account::IS_INVOICING     => self::TITLE_IS_INVOICING,
        self::KEY_CADASTRE_NUMBER => self::TITLE_CADASTRE_NUMBER,
    ];

    public function __construct(AccountDTO $entity)
    {
        $exData = $entity->getExData();

        $this->initProperties($entity, $entity->getId());
        $this->addProperties([
            self::KEY_CADASTRE_NUMBER => $exData->getCadastreNumber(),
        ]);

        $this->expandedProperties = [
            Account::IS_VERIFIED  => $this->getYesNoText($entity->isVerified()),
            Account::IS_INVOICING => $this->getYesNoText($entity->isInvoicing()),
        ];
    }
}
