<?php declare(strict_types=1);

namespace Core\Domains\Billing\Service\Models;

use App\Models\Billing\Service;
use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;

class ServiceComparator extends AbstractComparatorDTO
{
    public const TITLE_TYPE      = 'Тип';
    public const TITLE_PERIOD_ID = 'Период';
    public const TITLE_NAME      = 'Название';
    public const TITLE_COST      = 'Стоимость';
    public const TITLE_ACTIVE    = 'Активен';

    protected const KEYS_TO_TITLES = [
        Service::TYPE      => self::TITLE_TYPE,
        Service::PERIOD_ID => self::TITLE_PERIOD_ID,
        Service::NAME      => self::TITLE_NAME,
        Service::COST      => self::TITLE_COST,
        Service::ACTIVE    => self::TITLE_ACTIVE,
    ];

    public function __construct(ServiceDTO $entity)
    {
        $this->initProperties($entity, $entity->getId());

        $this->expandedProperties = [
            Service::TYPE   => $entity->getType()?->name(),
            Service::ACTIVE => $this->getYesNoText($entity->isActive()),
        ];
    }
}
