<?php declare(strict_types=1);

namespace Core\Domains\Option\Models\Comparators;

use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;
use Core\Domains\Option\Models\DataDTO\SntAccounting;
use Core\Domains\Option\Models\OptionDTO;

class SntAccountingComparator extends AbstractComparatorDTO
{
    public const string TITLE_BANK = 'Банк';
    public const string TITLE_ACC  = 'Расчетный счет';
    public const string TITLE_CORR = 'Корреспондентский счет';
    public const string TITLE_BIK  = 'БИК';
    public const string TITLE_INN  = 'ИНН';
    public const string TITLE_KPP  = 'КПП';
    public const string TITLE_OGRN = 'ОГРН';

    protected const array KEYS_TO_TITLES = [
        'bank' => self::TITLE_BANK,
        'acc'  => self::TITLE_ACC,
        'corr' => self::TITLE_CORR,
        'bik'  => self::TITLE_BIK,
        'inn'  => self::TITLE_INN,
        'kpp'  => self::TITLE_KPP,
        'ogrn' => self::TITLE_OGRN,
    ];

    public function __construct(OptionDTO $entity)
    {
        $this->initProperties($entity, $entity->getId());

        if ($entity->getData() instanceof SntAccounting) {
            $data                     = $entity->getData();
            $this->expandedProperties = [
                'bank' => $data?->getBank(),
                'acc'  => $data?->getAcc(),
                'corr' => $data?->getCorr(),
                'bik'  => $data?->getBik(),
                'inn'  => $data?->getInn(),
                'kpp'  => $data?->getKpp(),
                'ogrn' => $data?->getOgrn(),
            ];
        }
        else {
            $this->expandedProperties = [];
        }
    }
} 