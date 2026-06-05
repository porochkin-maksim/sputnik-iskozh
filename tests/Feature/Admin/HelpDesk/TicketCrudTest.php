<?php declare(strict_types=1);

namespace Tests\Feature\Admin\HelpDesk;

use App\Models\HelpDesk\Ticket;
use App\Models\HelpDesk\TicketCategory;
use App\Models\HelpDesk\TicketService;
use App\Models\User;
use App\Models\Account\Account;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Tests\Feature\FeatureTestCase;
use Tests\Feature\WithAdminAccess;

class TicketCrudTest extends FeatureTestCase
{
    use WithAdminAccess;

    private User           $admin;
    private Ticket         $ticket;
    private TicketCategory $category;
    private TicketService  $service;
    private Account        $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = TicketCategory::factory()->create([
            'type' => TicketTypeEnum::QUESTION,
        ]);

        $this->service = TicketService::factory()->create([
            'category_id' => $this->category->id,
            'code'        => 'repair',
        ]);

        $this->admin = $this->createAdminUser();

        $this->account = Account::factory()->create();

        $this->ticket = Ticket::factory()->create([
            'category_id' => $this->category->id,
            'service_id'  => $this->service->id,
            'user_id'     => $this->admin->id,
            'account_id'  => $this->account->id,
        ]);
    }

    public function test_list_tickets(): void
    {
        Ticket::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/admin/help-desk/tickets/ajax/list')
        ;

        $response->assertOk();
    }

    public function test_create_ticket(): void
    {
        $ticket = Ticket::factory()->create([
            'category_id' => $this->category->id,
            'service_id'  => $this->service->id,
            'user_id'     => $this->admin->id,
            'account_id'  => $this->account->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson('/admin/help-desk/tickets/ajax/save', [
                'id'            => $ticket->id,
                'type'          => $ticket->type->value,
                'category_id'   => $ticket->category_id,
                'service_id'    => $ticket->service_id,
                'priority'      => $ticket->priority->value,
                'status'        => $ticket->status->value,
                'description'   => 'Updated from create test',
                'contact_name'  => $ticket->contact_name,
                'contact_phone' => $ticket->contact_phone,
                'contact_email' => $ticket->contact_email,
                'account_id'    => $this->account->id,
                'user_id'       => $this->admin->id,
            ])
        ;

        $response->assertOk();

        $this->assertDatabaseHas('tickets', [
            'id'          => $ticket->id,
            'description' => 'Updated from create test',
        ]);
    }

    public function test_update_ticket(): void
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/admin/help-desk/tickets/ajax/save', [
                'id'            => $this->ticket->id,
                'type'          => $this->ticket->type->value,
                'category_id'   => $this->ticket->category_id,
                'service_id'    => $this->service->id,
                'priority'      => $this->ticket->priority->value,
                'status'        => $this->ticket->status->value,
                'description'   => 'Updated description',
                'contact_name'  => 'Updated Name',
                'contact_phone' => $this->ticket->contact_phone,
                'contact_email' => $this->ticket->contact_email,
                'account_id'    => $this->account->id,
                'user_id'       => $this->admin->id,
            ])
        ;

        $response->assertOk();

        $this->assertDatabaseHas('tickets', [
            'id'          => $this->ticket->id,
            'description' => 'Updated description',
        ]);
    }

    public function test_delete_ticket(): void
    {
        $response = $this->actingAs($this->admin)
            ->deleteJson("/admin/help-desk/tickets/ajax/delete/{$this->ticket->id}")
        ;

        $response->assertOk();

        $this->assertDatabaseMissing('tickets', [
            'id' => $this->ticket->id,
        ]);
    }

    public function test_view_ticket(): void
    {
        $response = $this->actingAs($this->admin)
            ->get("/admin/help-desk/tickets/view/{$this->ticket->id}")
        ;

        $response->assertOk();
    }

    public function test_unauthorized_access_returns_redirect(): void
    {
        $response = $this->get('/admin/help-desk/tickets/ajax/list');

        $response->assertStatus(302);
    }
}
