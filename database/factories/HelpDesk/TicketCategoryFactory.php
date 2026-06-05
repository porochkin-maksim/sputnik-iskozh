<?php declare(strict_types=1);

namespace Database\Factories\HelpDesk;

use App\Models\HelpDesk\TicketCategory;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketCategoryFactory extends Factory
{
    protected $model = TicketCategory::class;

    public function definition(): array
    {
        return [
            TicketCategory::TYPE       => TicketTypeEnum::QUESTION,
            TicketCategory::NAME       => fake()->word(),
            TicketCategory::CODE       => fake()->unique()->slug(1),
            TicketCategory::SORT_ORDER => fake()->numberBetween(0, 100),
            TicketCategory::IS_ACTIVE  => true,
        ];
    }
}
