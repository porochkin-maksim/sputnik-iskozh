<?php declare(strict_types=1);

namespace Core\Domains\Infra\Comparator\DTO;

class Difference implements DifferenceInterface
{
    /** @var mixed */
    protected $value1;

    /** @var mixed */
    protected $value2;

    /** @var int|string|null */
    protected $index;

    public function __construct(
        $value1 = null,
        $value2 = null,
        $index = null
    )
    {
        $this->value1 = $value1;
        $this->value2 = $value2;
        $this->index  = $index;
    }

    public function getValue1()
    {
        return $this->value1;
    }

    public function getValue2()
    {
        return $this->value2;
    }

    /**
     * @return int|string|null
     */
    public function getIndex()
    {
        return $this->index;
    }
}