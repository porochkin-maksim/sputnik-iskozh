<?php declare(strict_types=1);

namespace Database\Factories\Account;

use App\Models\Account\Account;
use App\Models\Infra\ExData;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            Account::NUMBER       => fake()->unique()->numerify('###-###'),
            Account::SIZE         => fake()->numberBetween(200, 2000),
            Account::BALANCE      => fake()->randomFloat(2, -5000, 5000),
            Account::IS_VERIFIED  => true,
            Account::IS_INVOICING => true,
            Account::SORT_VALUE   => fake()->word(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Account $account) {
            ExData::factory()->account($account->id)->create();
        });
    }
}
