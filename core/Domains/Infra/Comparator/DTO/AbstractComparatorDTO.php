<?php declare(strict_types=1);

namespace Core\Domains\Infra\Comparator\DTO;

use ReflectionObject;

abstract class AbstractComparatorDTO
{
    protected const KEYS_TO_TITLES = [];

    protected ?int  $id;
    protected array $expandedProperties = [];

    public function getProperty(string $key): mixed
    {
        return $this->expandedProperties[$key] ?? $this->{$key} ?? null;
    }

    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * @return array<string, string>
     */
    public static function getKeysToTitles(): array
    {
        return static::KEYS_TO_TITLES;
    }

    /**
     * @return string[]
     */
    public function getPropertiesKeys(): array
    {
        return array_keys(self::getKeysToTitles());
    }

    protected function initProperties(mixed $object, ?int $id): void
    {
        $this->id = $id;

        $reflectionObject = new ReflectionObject($object);

        foreach ($this->getPropertiesKeys() as $property) {
            if ( ! $reflectionObject->hasProperty($property)) {
                continue;
            }

            $reflectionProperty = $reflectionObject->getProperty($property);
            $isPublic           = $reflectionProperty->isPublic();

            if ( ! $isPublic) {
                $reflectionProperty->setAccessible(true);
            }

            $this->{$property} = $reflectionProperty->getValue($object);

            if ( ! $isPublic) {
                $reflectionProperty->setAccessible(false);
            }
        }
    }

    protected function getYesNoText(null|bool|int $value): ?string
    {
        return $value ? 'Да' : 'Нет';
    }
}
