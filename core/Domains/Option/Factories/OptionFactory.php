<?php declare(strict_types=1);

namespace Core\Domains\Option\Factories;

use App\Models\Infra\Option;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\Models\DataDTO\CounterReadingDay;
use Core\Domains\Option\Models\DataDTO\DataDTOInterface;
use Core\Domains\Option\Models\DataDTO\SntAccounting;
use Core\Domains\Option\Models\OptionDTO;

class OptionFactory
{
    public function makeDefault(): OptionDTO
    {
        return new OptionDTO();
    }

    public function makeByType(OptionEnum $type): OptionDTO
    {
        $dto = $this->makeDefault()
            ->setId($type->value)
            ->setType($type)
        ;

        $dataDTO = $this->createDataByType($type);
        if ($dataDTO) {
            $dto->setData($dataDTO);
        }

        return $dto;
    }

    public function makeModelFromDto(OptionDTO $dto, ?Option $model = null): Option
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = Option::make();
        }

        return $result->fill([
            Option::ID   => $dto->getId(),
            Option::DATA => $dto->getData()?->jsonSerialize(),
        ]);
    }

    public function makeDtoFromObject(Option $model): OptionDTO
    {
        $result = new OptionDTO();
        $type   = OptionEnum::tryFrom($model->id);

        $result
            ->setId($model->id)
            ->setType($type)
        ;

        if ( ! empty($model->data) && $type) {
            $dataDTO = $this->createDataByType($type);

            if ($dataDTO) {
                // Map the properties from model->data to the DataDTO
                foreach ($model->data as $key => $value) {
                    $setter = 'set' . ucfirst($key);
                    if (method_exists($dataDTO, $setter)) {
                        $dataDTO->$setter($value);
                    }
                }

                $result->setData($dataDTO);
            }
        }
        else {
            $result->setData(null);
        }

        $result
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at)
        ;

        return $result;
    }

    /**
     * Create the appropriate DataDTO based on the option type
     */
    protected function createDataByType(OptionEnum $type): ?DataDTOInterface
    {
        return match ($type) {
            OptionEnum::SNT_ACCOUNTING      => new SntAccounting(),
            OptionEnum::COUNTER_READING_DAY => new CounterReadingDay(),
            default                         => null,
        };
    }

    public function makeDataDtoFromArray(?OptionEnum $type, array $data): ?DataDTOInterface
    {
        if ( ! $type) {
            return null;
        }

        $dataDTO = $this->createDataByType($type);
        if ( ! $dataDTO) {
            return null;
        }

        $reflection = new \ReflectionClass($dataDTO);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);

        foreach ($data as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($dataDTO, $setter)) {
                // Находим свойство по имени
                $property = array_filter($properties, fn($prop) => $prop->getName() === $key);
                if ($property) {
                    $property     = reset($property);
                    $propertyType = $property->getType();

                    // Если значение не null и тип свойства определен
                    if ($value !== null && $propertyType instanceof \ReflectionNamedType) {
                        $typeName = $propertyType->getName();

                        // Приводим значение к нужному типу
                        $value = match ($typeName) {
                            'int'    => (int) $value,
                            'float'  => (float) $value,
                            'bool'   => (bool) $value,
                            'string' => (string) $value,
                            default  => $value
                        };
                    }
                }

                $dataDTO->$setter($value);
            }
        }

        return $dataDTO;
    }
}