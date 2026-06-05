<?php declare(strict_types=1);

namespace Tests\Feature\Admin\Billing;

use App\Models\Account\Account;
use App\Models\User;
use Tests\Feature\FeatureTestCase;
use Tests\Feature\WithAdminAccess;

class AccountTest extends FeatureTestCase
{
    use WithAdminAccess;

    private User    $admin;
    private Account $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin   = $this->createAdminUser();
        $this->account = Account::factory()->create();

        \lc::reset();
    }

    public function test_list_accounts(): void
    {
        $this->actingAs($this->admin);
        \lc::reset();

        Account::factory()->count(3)->create();

        $response = $this->getJson('/admin/accounts/json/list');

        if ( ! $response->isOk()) {
            dump($response->getContent());
        }

        $response->assertOk();
        $response->assertJsonStructure(['accounts', 'allAccounts', 'total', 'historyUrl']);
    }

    public function test_create_account(): void
    {
        \lc::reset();
        $response = $this->actingAs($this->admin)
            ->postJson('/admin/accounts/json/save', [
                'number'         => '123-456',
                'size'           => 500,
                'is_invoicing'   => true,
                'cadastreNumber' => '77:01:000102:1234',
            ])
        ;

        if ( ! $response->isOk()) {
            dump($response->getContent());
        }

        $response->assertOk();

        $this->assertDatabaseHas('accounts', [
            'number'       => '123-456',
            'size'         => 500,
            'is_invoicing' => true,
        ]);
    }

    public function test_get_account(): void
    {
        \lc::reset();
        $response = $this->actingAs($this->admin)
            ->getJson("/admin/accounts/json/view/{$this->account->id}")
        ;

        if ( ! $response->isOk()) {
            dump($response->getContent());
        }

        $response->assertOk();
        $response->assertJsonStructure(['id', 'number', 'size', 'balance', 'isInvoicing', 'actions', 'users']);
    }

    public function test_view_account_page(): void
    {
        \lc::reset();
        $response = $this->actingAs($this->admin)
            ->get("/admin/accounts/view/{$this->account->id}")
        ;

        $response->assertOk();
    }

    public function test_unauthorized_access_returns_redirect(): void
    {
        $response = $this->getJson('/admin/accounts/json/list');

        $response->assertStatus(401);
    }
}
