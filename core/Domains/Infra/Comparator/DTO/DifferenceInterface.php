<?php declare(strict_types=1);

namespace Core\Domains\Infra\Comparator\DTO;

interface DifferenceInterface
{
    public function getValue1();

    public function getValue2();

    /**
     * @return int|string|null - property name or array index
     */
    public function getIndex();
}