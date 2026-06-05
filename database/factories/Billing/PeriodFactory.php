<?php declare(strict_types=1);

namespace Database\Factories\Billing;

use App\Models\Billing\Period;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeriodFactory extends Factory
{
    protected $model = Period::class;

    public function definition(): array
    {
        $year = fake()->numberBetween(2020, 2030);

        return [
            Period::NAME      => sprintf('%d год', $year),
            Period::START_AT  => Carbon::create($year, 1, 1),
            Period::END_AT    => Carbon::create($year, 12, 31),
            Period::IS_CLOSED => false,
        ];
    }
}
