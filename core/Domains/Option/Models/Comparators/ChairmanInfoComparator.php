<?php declare(strict_types=1);

namespace Core\Domains\Option\Models\Comparators;

use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;
use Core\Domains\Option\Models\DataDTO\ChairmanInfo;
use Core\Domains\Option\Models\DataDTO\SntAccounting;
use Core\Domains\Option\Models\OptionDTO;

class ChairmanInfoComparator extends AbstractComparatorDTO
{
    public const string TITLE_LAST_NAME   = 'Фамилия';
    public const string TITLE_FIRST_NAME  = 'Имя';
    public const string TITLE_MIDDLE_NAME = 'Отчество';
    public const string TITLE_PHONE       = 'Телефон';
    public const string TITLE_EMAIL       = 'Почта';

    protected const array KEYS_TO_TITLES = [
        'lastName'   => self::TITLE_LAST_NAME,
        'firstName'  => self::TITLE_FIRST_NAME,
        'middleName' => self::TITLE_MIDDLE_NAME,
        'phone'      => self::TITLE_PHONE,
        'email'      => self::TITLE_EMAIL,
    ];

    public function __construct(OptionDTO $entity)
    {
        $this->initProperties($entity, $entity->getId());

        if ($entity->getData() instanceof ChairmanInfo) {
            $data                     = $entity->getData();
            $this->expandedProperties = [
                'lastName'   => $data?->getLastName(),
                'firstName'  => $data?->getFirstName(),
                'middleName' => $data?->getMiddleName(),
                'phone'      => $data?->getPhone(),
                'email'      => $data?->getEmail(),
            ];
        }
        else {
            $this->expandedProperties = [];
        }
    }
}