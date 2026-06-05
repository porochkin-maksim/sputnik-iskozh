<?php declare(strict_types=1);

namespace Tests\Feature\Admin\Billing;

use App\Models\Billing\Period;
use App\Models\User;
use Tests\Feature\FeatureTestCase;
use Tests\Feature\WithAdminAccess;

class PeriodTest extends FeatureTestCase
{
    use WithAdminAccess;

    private User $admin;

    private Period $period;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->createAdminUser();
        $this->period = Period::factory()->create();

        \lc::reset();
    }

    public function test_list_periods(): void
    {
        $this->actingAs($this->admin);
        \lc::reset();

        Period::factory()->count(3)->create();

        $response = $this->getJson('/admin/periods/json/list');

        if (! $response->isOk()) {
            dump($response->getContent());
        }

        $response->assertOk();
        $response->assertJsonStructure(['periods', 'historyUrl']);
    }

    public function test_create_period(): void
    {
        \lc::reset();
        $response = $this->actingAs($this->admin)
            ->postJson('/admin/periods/json/save', [
                'name' => '2025 год',
                'start_at' => '2025-01-01',
                'end_at' => '2025-12-31',
                'is_closed' => false,
            ]);

        if (! $response->isOk()) {
            dump($response->getContent());
        }

        $response->assertOk();

        $this->assertDatabaseHas('periods', [
            'name' => '2025 год',
        ]);
    }

    public function test_delete_period(): void
    {
        \lc::reset();
        $response = $this->actingAs($this->admin)
            ->deleteJson("/admin/periods/json/{$this->period->id}");

        if (! $response->isOk()) {
            dump($response->getContent());
        }

        $response->assertOk();

        $this->assertSoftDeleted($this->period);
    }

    public function test_unauthorized_access_returns_redirect(): void
    {
        $response = $this->getJson('/admin/periods/json/list');

        $response->assertStatus(401);
    }
}
