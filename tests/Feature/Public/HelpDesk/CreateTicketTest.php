<?php declare(strict_types=1);

namespace Tests\Feature\Public\HelpDesk;

use App\Models\HelpDesk\TicketCategory;
use App\Models\HelpDesk\TicketService;
use App\Models\Account\Account;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Tests\Feature\FeatureTestCase;

class CreateTicketTest extends FeatureTestCase
{
    private TicketCategory $category;
    private TicketService  $service;
    private Account        $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = TicketCategory::factory()->create([
            'type' => TicketTypeEnum::QUESTION,
            'code' => 'electric',
        ]);

        $this->service = TicketService::factory()->create([
            'category_id' => $this->category->id,
            'code'        => 'repair',
        ]);

        $this->account = Account::factory()->create();
    }

    public function test_creates_ticket_successfully(): void
    {
        $response = $this->post(
            '/contacts/requests/help-desk/question/electric/repair',
            [
                'consent'     => '1',
                'account_id'  => $this->account->id,
                'description' => 'Test issue description',
                'name'        => 'Ivan Ivanov',
                'phone'       => '+79991234567',
                'email'       => 'ivan@test.com',
            ],
        );

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('tickets', [
            'description'   => 'Test issue description',
            'contact_name'  => 'Ivan Ivanov',
            'contact_phone' => '+79991234567',
            'contact_email' => 'ivan@test.com',
        ]);
    }

    public function test_validates_required_fields(): void
    {
        $response = $this->post(
            '/contacts/requests/help-desk/question/electric/repair',
            [
                'consent' => '1',
            ],
        );

        $response->assertStatus(500);
    }

    public function test_returns_404_for_invalid_type(): void
    {
        $response = $this->post(
            '/contacts/requests/help-desk/invalid-type/electric/repair',
            [
                'consent'     => '1',
                'description' => 'Test',
                'name'        => 'Ivan',
                'phone'       => '+79991234567',
                'email'       => 'ivan@test.com',
            ],
        );

        $response->assertStatus(500);
    }

    public function test_returns_404_for_invalid_category(): void
    {
        $response = $this->post(
            '/contacts/requests/help-desk/question/invalid-category/repair',
            [
                'consent'     => '1',
                'description' => 'Test',
                'name'        => 'Ivan',
                'phone'       => '+79991234567',
                'email'       => 'ivan@test.com',
            ],
        );

        $response->assertStatus(500);
    }

    public function test_returns_404_for_invalid_service(): void
    {
        $response = $this->post(
            '/contacts/requests/help-desk/question/electric/invalid-service',
            [
                'consent'     => '1',
                'description' => 'Test',
                'name'        => 'Ivan',
                'phone'       => '+79991234567',
                'email'       => 'ivan@test.com',
            ],
        );

        $response->assertStatus(500);
    }
}
