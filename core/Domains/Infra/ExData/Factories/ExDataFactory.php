<?php declare(strict_types=1);

namespace Core\Domains\Infra\ExData\Factories;

use Core\Domains\Infra\ExData\ExDataEntity;
use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;

class ExDataFactory
{
    public function makeDefault(ExDataTypeEnum $type): ExDataEntity
    {
        return (new ExDataEntity())->setType($type);
    }

    public function makeByType(ExDataTypeEnum $type, int $referenceId): ExDataEntity
    {
        return $this->makeDefault($type)
            ->setReferenceId($referenceId);
    }

} 
