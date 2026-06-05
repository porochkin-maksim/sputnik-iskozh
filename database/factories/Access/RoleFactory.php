<?php declare(strict_types=1);

namespace Database\Factories\Access;

use App\Models\Access\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            Role::NAME => fake()->word(),
        ];
    }
}
