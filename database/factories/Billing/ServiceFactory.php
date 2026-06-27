<?php declare(strict_types=1);

namespace Database\Factories\Billing;

use App\Models\Billing\Period;
use App\Models\Billing\Service;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $startAt = fake()->dateTimeBetween('-1 month', '+1 month');

        return [
            Service::PERIOD_ID   => Period::factory(),
            Service::TYPE        => ServiceTypeEnum::MEMBERSHIP_FEE->value,
            Service::NAME        => fake()->word(),
            Service::COST        => fake()->randomFloat(2, 100, 10000),
            Service::PERIOD_FROM => $startAt,
            Service::PERIOD_TO   => (clone $startAt)->modify('+1 month'),
        ];
    }
}
