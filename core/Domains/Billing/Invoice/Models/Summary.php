<?php

namespace Core\Domains\Billing\Invoice\Models;

class Summary implements \JsonSerializable
{
    private float $incomeCost;
    private float $incomePayed;
    private float $outcomeCost;
    private float $outcomePayed;

    public function __construct(array $data)
    {
        $this->incomeCost   = abs((float) $data['income_cost']);
        $this->incomePayed  = abs((float) $data['income_payed']);
        $this->outcomeCost  = abs((float) $data['outcome_cost']);
        $this->outcomePayed = abs((float) $data['outcome_payed']);
    }

    public function jsonSerialize(): array
    {
        return [
            'incomeCost'   => $this->incomeCost,
            'incomePayed'  => $this->incomePayed,
            'outcomeCost'  => $this->outcomeCost,
            'outcomePayed' => $this->outcomePayed,
        ];
    }
}