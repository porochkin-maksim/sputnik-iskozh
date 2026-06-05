<?php declare(strict_types=1);

namespace Database\Factories\HelpDesk;

use App\Models\HelpDesk\Ticket;
use App\Models\HelpDesk\TicketCategory;
use App\Models\User;
use Core\Domains\HelpDesk\Enums\TicketPriorityEnum;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            Ticket::USER_ID       => User::factory(),
            Ticket::TYPE          => TicketTypeEnum::QUESTION,
            Ticket::CATEGORY_ID   => TicketCategory::factory(),
            Ticket::PRIORITY      => TicketPriorityEnum::MEDIUM,
            Ticket::STATUS        => TicketStatusEnum::NEW,
            Ticket::DESCRIPTION   => fake()->text(200),
            Ticket::CONTACT_NAME  => fake()->name(),
            Ticket::CONTACT_PHONE => fake()->phoneNumber(),
            Ticket::CONTACT_EMAIL => fake()->email(),
        ];
    }
}
