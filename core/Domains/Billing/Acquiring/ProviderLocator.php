<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring;

use Core\Domains\Billing\Acquiring\Providers\VTBProvider;
use Core\Services\External\VTB\ApiConfig;
use Core\Services\External\VTB\VTBLocator;

abstract class ProviderLocator
{
    public static function VTB(): VTBProvider
    {
        return new VTBProvider(
            VTBLocator::api(
                new ApiConfig(
                    config('external.api.providers.VTB.address'),
                    config('external.api.providers.VTB.username'),
                    config('external.api.providers.VTB.password'),
                    config('external.api.providers.VTB.token'),
                ),
            ),
        );
    }
}
