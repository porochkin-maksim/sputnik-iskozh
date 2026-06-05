<?php declare(strict_types=1);

namespace Tests\Feature\Admin\Billing;

use App\Models\Billing\Period;
use App\Models\User;
use Tests\Feature\FeatureTestCase;
use Tests\Feature\WithAdminAccess;

class InvoiceImportTest extends FeatureTestCase
{
    use WithAdminAccess;

    private User   $admin;
    private Period $period;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createAdminUser();

        $this->period = Period::factory()->create();

        \lc::reset();
    }

    public function test_import_page_renders(): void
    {
        \lc::reset();
        $response = $this->actingAs($this->admin)
            ->get("/admin/invoices/import-payments/period-{$this->period->id}/")
        ;

        $response->assertOk();
    }

    public function test_unauthorized_access_returns_redirect(): void
    {
        $response = $this->get("/admin/invoices/import-payments/period-{$this->period->id}/");

        $response->assertStatus(302);
    }
}
