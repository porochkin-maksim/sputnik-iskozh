<?php declare(strict_types=1);

namespace Core\Domains\Account\Models;

use App\Models\Account\Account;
use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;
use Core\Enums\DateTimeFormat;

class AccountComparator extends AbstractComparatorDTO
{
    public const TITLE_NUMBER          = 'Номер';
    public const TITLE_SIZE            = 'Площадь';
    public const TITLE_BALANCE         = 'Баланс';
    public const TITLE_IS_VERIFIED     = 'Подтверждён';
    public const TITLE_CADASTRE_NUMBER = 'Кадастровый номер';
    public const TITLE_REGISTRY_DATE   = 'Дата регистрации';

    private const KEY_CADASTRE_NUMBER = 'cadastreNumber';
    private const KEY_REGISTRY_DATE   = 'registryDate';

    protected const KEYS_TO_TITLES = [
        Account::NUMBER           => self::TITLE_NUMBER,
        Account::SIZE             => self::TITLE_SIZE,
        Account::BALANCE          => self::TITLE_BALANCE,
        Account::IS_VERIFIED      => self::TITLE_IS_VERIFIED,
        self::KEY_CADASTRE_NUMBER => self::TITLE_CADASTRE_NUMBER,
        self::KEY_REGISTRY_DATE   => self::TITLE_REGISTRY_DATE,
    ];

    public function __construct(AccountDTO $entity)
    {
        $exData = $entity->getExData();

        $this->initProperties($entity, $entity->getId());
        $this->addProperties([
            self::KEY_CADASTRE_NUMBER => $exData->getCadastreNumber(),
            self::KEY_REGISTRY_DATE   => $exData->getRegistryDate()?->format(DateTimeFormat::DATE_DEFAULT),
        ]);

        $this->expandedProperties = [
            Account::IS_VERIFIED => $this->getYesNoText($entity->isVerified()),
        ];
    }
}
