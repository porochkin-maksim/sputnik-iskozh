<?php declare(strict_types=1);

namespace Core\Domains\Billing\Summary\Models;

use App\Services\Money\MoneyService;

class Summary implements \JsonSerializable
{
    private float $incomeCost;
    private float $incomePaid;
    private float $outcomeCost;
    private float $outcomePaid;

    private int $regularCount;
    private int $incomeCount;
    private int $outcomeCount;

    public function __construct(array $data)
    {
        $this->incomeCost  = abs((float) $data['incomeCost']);
        $this->incomePaid  = abs((float) $data['incomePaid']);
        $this->outcomeCost = abs((float) $data['outcomeCost']);
        $this->outcomePaid = abs((float) $data['outcomePaid']);

        $this->regularCount = (int) $data['regularCount'];
        $this->incomeCount  = (int) $data['incomeCount'];
        $this->outcomeCount = (int) $data['outcomeCount'];
    }

    public function jsonSerialize(): array
    {
        $incomeCost  = MoneyService::parse($this->incomeCost);
        $incomePaid  = MoneyService::parse($this->incomePaid);
        $outcomeCost = MoneyService::parse($this->outcomeCost);
        $outcomePaid = MoneyService::parse($this->outcomePaid);

        return [
            'incomeCost'  => MoneyService::toFloat($incomeCost),
            'incomePaid'  => MoneyService::toFloat($incomePaid),
            'outcomeCost' => MoneyService::toFloat($outcomeCost),
            'outcomePaid' => MoneyService::toFloat($outcomePaid),

            'deltaIncome'  => MoneyService::toFloat($incomeCost->subtract($incomePaid)),
            'deltaOutcome' => MoneyService::toFloat($outcomeCost->subtract($outcomePaid)),

            'deltaCost' => MoneyService::toFloat($incomeCost->subtract($outcomeCost)),
            'deltaPaid' => MoneyService::toFloat($incomePaid->subtract($outcomePaid)),
            'delta'     => MoneyService::toFloat($incomeCost->subtract($outcomeCost)->subtract($incomePaid->subtract($outcomePaid))),

            'regularCount' => $this->regularCount,
            'incomeCount'  => $this->incomeCount,
            'outcomeCount' => $this->outcomeCount,

            'totalCount' => $this->regularCount + $this->incomeCount + $this->outcomeCount,
        ];
    }
}