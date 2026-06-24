<?php declare(strict_types=1);

namespace Database\Factories\Billing;

use App\Models\Account\Account;
use App\Models\Billing\Invoice;
use App\Models\Billing\Period;
use App\Models\Billing\Service;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            Invoice::PERIOD_ID  => Period::factory(),
            Invoice::ACCOUNT_ID => Account::factory(),
            Invoice::SERVICE_ID => Service::factory(),
            Invoice::TYPE       => InvoiceTypeEnum::REGULAR->value,
            Invoice::COST       => fake()->randomFloat(2, 500, 50000),
            Invoice::PAID       => 0,
            Invoice::DEBT       => 0,
        ];
    }
}
