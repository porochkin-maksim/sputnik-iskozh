<?php

namespace Core\Domains\Billing\Invoice\Models;

use Core\Services\Money\MoneyService;

class Summary implements \JsonSerializable
{
    private float $incomeCost;
    private float $incomePayed;
    private float $outcomeCost;
    private float $outcomePayed;

    private int $regularCount;
    private int $incomeCount;
    private int $outcomeCount;

    public function __construct(array $data)
    {
        $this->incomeCost   = abs((float) $data['incomeCost']);
        $this->incomePayed  = abs((float) $data['incomePayed']);
        $this->outcomeCost  = abs((float) $data['outcomeCost']);
        $this->outcomePayed = abs((float) $data['outcomePayed']);

        $this->regularCount = (int) $data['regularCount'];
        $this->incomeCount  = (int) $data['incomeCount'];
        $this->outcomeCount = (int) $data['outcomeCount'];
    }

    public function jsonSerialize(): array
    {
        $incomeCost   = MoneyService::parse($this->incomeCost);
        $incomePayed  = MoneyService::parse($this->incomePayed);
        $outcomeCost  = MoneyService::parse($this->outcomeCost);
        $outcomePayed = MoneyService::parse($this->outcomePayed);

        return [
            'incomeCost'   => MoneyService::toFloat($incomeCost),
            'incomePayed'  => MoneyService::toFloat($incomePayed),
            'outcomeCost'  => MoneyService::toFloat($outcomeCost),
            'outcomePayed' => MoneyService::toFloat($outcomePayed),

            'deltaIncome'  => MoneyService::toFloat($incomeCost->subtract($incomePayed)),
            'deltaOutcome' => MoneyService::toFloat($outcomeCost->subtract($outcomePayed)),

            'deltaCost'  => MoneyService::toFloat($incomeCost->subtract($outcomeCost)),
            'deltaPayed' => MoneyService::toFloat($incomePayed->subtract($outcomePayed)),
            'delta'      => MoneyService::toFloat($incomeCost->subtract($outcomeCost)->subtract($incomePayed->subtract($outcomePayed))),

            'regularCount' => $this->regularCount,
            'incomeCount'  => $this->incomeCount,
            'outcomeCount' => $this->outcomeCount,

            'totalCount' => $this->regularCount + $this->incomeCount + $this->outcomeCount,
        ];
    }
}