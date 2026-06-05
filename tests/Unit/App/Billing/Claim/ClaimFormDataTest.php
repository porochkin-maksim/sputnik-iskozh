<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Claim;

use Core\App\Billing\Claim\ClaimFormData;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Service\ServiceCollection;
use Tests\TestCase;

class ClaimFormDataTest extends TestCase
{
    public function test_constructor_sets_properties(): void
    {
        $claim    = new ClaimEntity;
        $services = new ServiceCollection;
        $select   = [1 => 'Услуга 1', 2 => 'Услуга 2'];

        $data = new ClaimFormData($select, $claim, $services);

        $this->assertSame($select, $data->servicesSelect);
        $this->assertSame($claim, $data->claim);
        $this->assertSame($services, $data->services);
    }
}
