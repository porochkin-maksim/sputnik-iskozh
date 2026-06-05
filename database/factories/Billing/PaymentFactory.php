<?php declare(strict_types=1);

namespace Database\Factories\Billing;

use App\Models\Account\Account;
use App\Models\Billing\Invoice;
use App\Models\Billing\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            Payment::INVOICE_ID => Invoice::factory(),
            Payment::ACCOUNT_ID => Account::factory(),
            Payment::COST       => fake()->randomFloat(2, 100, 10000),
            Payment::PAID_AT    => fake()->dateTimeThisYear(),
        ];
    }
}
