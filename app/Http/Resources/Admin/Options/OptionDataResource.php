<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Options;

use Core\Domains\Option\Factories\ComparatorFactory;
use Core\Domains\Option\Models\DataDTO\DataDTOInterface;
use Core\Domains\Option\Models\OptionDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use ReflectionClass;

class OptionDataResource extends JsonResource
{
    private DataDTOInterface  $data;
    private ComparatorFactory $comparatorFactory;

    public function __construct(DataDTOInterface $data)
    {
        parent::__construct($data);
        $this->data              = $data;
        $this->comparatorFactory = new ComparatorFactory();
    }

    public function toArray(Request $request): array
    {
        // Создаем временный DTO для получения компаратора
        $tempDto = new OptionDTO();
        $tempDto->setData($this->data);

        // Получаем компаратор для данного типа данных
        $comparator = $this->comparatorFactory->createComparator($tempDto);

        // Получаем маппинг ключей к заголовкам из компаратора через рефлексию
        $keysToTitles = (new ReflectionClass($comparator))->getConstant('KEYS_TO_TITLES');

        if ( ! $keysToTitles) {
            return [
                'properties' => [],
                'raw'       => $this->data->jsonSerialize(),
            ];
        }

        // Получаем типы свойств через рефлексию
        $reflection = new \ReflectionClass($this->data);
        // Формируем массив свойств
        $properties = [];
        foreach ($keysToTitles as $key => $title) {
            $getter = 'get' . ucfirst($key);
            if (method_exists($this->data, $getter)) {
                // Находим свойство по имени для определения типа
                $property = array_filter($reflection->getProperties(\ReflectionProperty::IS_PRIVATE), 
                    static fn($prop) => $prop->getName() === $key
                );
                
                $inputType = 'text'; // Тип по умолчанию
                
                if ($property) {
                    $property = reset($property);
                    $propertyType = $property->getType();
                    
                    if ($propertyType instanceof \ReflectionNamedType) {
                        $typeName = $propertyType->getName();
                        
                        // Маппинг типов PHP в типы HTML input
                        $inputType = match($typeName) {
                            'int' => 'number',
                            'float' => 'number',
                            'bool' => 'checkbox',
                            'string' => 'text',
                            default => 'text'
                        };
                    }
                }

                $properties[] = [
                    'key'       => $key,
                    'title'     => $title,
                    'value'     => $this->data->$getter(),
                    'inputType' => $inputType,
                ];
            }
        }

        return [
            'properties' => $properties,
            'raw'       => $this->data->jsonSerialize(),
        ];
    }
} 