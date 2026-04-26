<?php declare(strict_types=1);

namespace Core\Domains\Option;

use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\Models\DataDTO\ChairmanInfo;
use Core\Domains\Option\Models\DataDTO\CounterReadingDay;
use Core\Domains\Option\Models\DataDTO\DataDTOInterface;
use Core\Domains\Option\Models\DataDTO\SntAccounting;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;

class OptionFactory
{
    public function makeDefault(): OptionEntity
    {
        return new OptionEntity();
    }

    public function makeByType(OptionEnum $type): OptionEntity
    {
        $entity = $this->makeDefault()
            ->setId($type->value)
            ->setType($type);

        $data = $this->createDataByType($type);
        if ($data) {
            $entity->setData($data);
        }

        return $entity;
    }

    public function makeDataDtoFromArray(?OptionEnum $type, array $data): ?DataDTOInterface
    {
        if ( ! $type) {
            return null;
        }

        $dataDto = $this->createDataByType($type);
        if ( ! $dataDto) {
            return null;
        }

        $reflection = new ReflectionClass($dataDto);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PRIVATE);

        foreach ($data as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if ( ! method_exists($dataDto, $setter)) {
                continue;
            }

            $property = array_filter($properties, static fn($prop) => $prop->getName() === $key);
            if ($property) {
                $property = reset($property);
                $propertyType = $property->getType();

                if ($value !== null && $propertyType instanceof ReflectionNamedType) {
                    $value = match ($propertyType->getName()) {
                        'int' => (int) $value,
                        'float' => (float) $value,
                        'bool' => (bool) $value,
                        'string' => (string) $value,
                        default => $value,
                    };
                }
            }

            $dataDto->$setter($value);
        }

        return $dataDto;
    }

    protected function createDataByType(OptionEnum $type): ?DataDTOInterface
    {
        return match ($type) {
            OptionEnum::SNT_ACCOUNTING => new SntAccounting(),
            OptionEnum::COUNTER_READING_DAY => new CounterReadingDay(),
            OptionEnum::CHAIRMAN_INFO => new ChairmanInfo(),
            default => null,
        };
    }
}
