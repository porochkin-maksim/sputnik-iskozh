<?php declare(strict_types=1);

namespace Database\Factories\Billing;

use App\Models\Billing\Claim;
use App\Models\Billing\Invoice;
use App\Models\Billing\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClaimFactory extends Factory
{
    protected $model = Claim::class;

    public function definition(): array
    {
        return [
            Claim::INVOICE_ID => Invoice::factory(),
            Claim::SERVICE_ID => Service::factory(),
            Claim::NAME => fake()->word(),
            Claim::TARIFF => fake()->randomFloat(2, 10, 500),
            Claim::COST => fake()->randomFloat(2, 100, 10000),
            Claim::PAID => 0,
            Claim::QUANTITY => fake()->numberBetween(1, 100),
        ];
    }
}
