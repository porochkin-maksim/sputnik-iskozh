<?php declare(strict_types=1);

namespace Tests\Feature\Admin\Billing;

use App\Jobs\Billing\CreateClaimsAndPaymentsForRegularInvoiceJob;
use App\Models\Account\Account;
use App\Models\Billing\Invoice;
use App\Models\Billing\Period;
use App\Models\User;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Illuminate\Support\Facades\Bus;
use Tests\Feature\FeatureTestCase;
use Tests\Feature\WithAdminAccess;

class InvoiceTest extends FeatureTestCase
{
    use WithAdminAccess;

    private User   $admin;
    private Period $period;

    private Account $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createAdminUser();

        $this->period  = Period::factory()->create();
        $this->account = Account::factory()->create();

        \lc::reset();
    }

    public function test_list_invoices(): void
    {
        $this->actingAs($this->admin);
        \lc::reset();

        Invoice::factory()->count(3)->create();

        $response = $this->getJson('/admin/invoices/json/list');

        $response->assertOk();
    }

    public function test_create_invoice(): void
    {
        $this->actingAs($this->admin);
        \lc::reset();

        $response = $this->postJson('/admin/invoices/json/save', [
            'period_id'  => $this->period->id,
            'account_id' => $this->account->id,
            'type'       => InvoiceTypeEnum::REGULAR->value,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('invoices', [
            'period_id'  => $this->period->id,
            'account_id' => $this->account->id,
            'cost'       => 0.0,
        ]);
    }

    public function test_get_invoice(): void
    {
        $this->actingAs($this->admin);
        \lc::reset();

        $invoice = Invoice::factory()->create();

        $response = $this->getJson("/admin/invoices/json/get/{$invoice->id}");

        $response->assertOk();
        $response->assertJsonFragment(['id' => $invoice->id]);
    }

    public function test_delete_invoice(): void
    {
        Bus::fake([CreateClaimsAndPaymentsForRegularInvoiceJob::class]);

        $this->actingAs($this->admin);
        \lc::reset();

        $invoice = Invoice::factory()->create();

        $response = $this->deleteJson("/admin/invoices/json/delete/{$invoice->id}");

        $response->assertOk();

        $this->assertDatabaseMissing('invoices', [
            'id' => $invoice->id,
        ]);
    }
}
