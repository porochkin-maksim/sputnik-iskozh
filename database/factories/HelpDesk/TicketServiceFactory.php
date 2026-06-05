<?php declare(strict_types=1);

namespace Database\Factories\HelpDesk;

use App\Models\HelpDesk\TicketCategory;
use App\Models\HelpDesk\TicketService;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketServiceFactory extends Factory
{
    protected $model = TicketService::class;

    public function definition(): array
    {
        return [
            TicketService::CATEGORY_ID => TicketCategory::factory(),
            TicketService::NAME        => fake()->word(),
            TicketService::CODE        => fake()->unique()->slug(1),
            TicketService::SORT_ORDER  => fake()->numberBetween(0, 100),
            TicketService::IS_ACTIVE   => true,
        ];
    }
}
