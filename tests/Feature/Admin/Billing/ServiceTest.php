<?php declare(strict_types=1);

namespace Tests\Feature\Admin\Billing;

use App\Models\Billing\Period;
use App\Models\Billing\Service;
use App\Models\User;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Tests\Feature\FeatureTestCase;
use Tests\Feature\WithAdminAccess;

class ServiceTest extends FeatureTestCase
{
    use WithAdminAccess;

    private User    $admin;
    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin   = $this->createAdminUser();
        $this->service = Service::factory()->create();

        \lc::reset();
    }

    public function test_list_services(): void
    {
        $this->actingAs($this->admin);
        \lc::reset();

        Service::factory()->count(3)->create();

        $response = $this->getJson('/admin/services/json/list');

        $response->assertOk();
        $response->assertJsonStructure(['services', 'periods', 'periodsInfo', 'types', 'historyUrl']);
    }

    public function test_create_service(): void
    {
        $period = Period::factory()->create();
        $periodFrom = '2026-01-01 00:00:00';
        $periodTo   = '2026-12-31 23:59:59';

        $this->actingAs($this->admin);
        \lc::reset();
        $response = $this->postJson('/admin/services/json/save', [
                'period_id'   => $period->id,
                'type'        => ServiceTypeEnum::MEMBERSHIP_FEE->value,
                'name'        => 'Test Service',
                'cost'        => 1500.50,
                'period_from' => $periodFrom,
                'period_to'   => $periodTo,
            ])
        ;

        $response->assertOk();

        $this->assertDatabaseHas('services', [
            'period_id'   => $period->id,
            'type'        => ServiceTypeEnum::MEMBERSHIP_FEE->value,
            'name'        => 'Test Service',
            'cost'        => 1500.50,
            'period_from' => $periodFrom,
            'period_to'   => $periodTo,
        ]);
    }

    public function test_delete_service(): void
    {
        $this->actingAs($this->admin);
        \lc::reset();
        $response = $this->deleteJson("/admin/services/json/{$this->service->id}")
        ;

        $response->assertOk();

        $this->assertSoftDeleted($this->service);
    }

    public function test_unauthorized_access_returns_redirect(): void
    {
        $response = $this->getJson('/admin/services/json/list');

        $response->assertStatus(401);
    }
}
