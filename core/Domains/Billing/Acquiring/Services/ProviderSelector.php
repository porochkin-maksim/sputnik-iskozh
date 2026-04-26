<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Services;

use Core\Domains\Billing\Acquiring\Contracts\ProviderInterface;
use Core\Domains\Billing\Acquiring\Enums\ProviderEnum;
use Core\Domains\Billing\Acquiring\Exceptions\UndefinedProviderException;
use Core\Domains\Billing\Acquiring\Providers\VTB\VTBProvider;

class ProviderSelector
{
    private const array PROBABILITIES = [
        // ProviderEnum::VTB->value => 100,
    ];

    public function __construct(
        private readonly VTBProvider $vtbProvider,
    )
    {
    }

    /**
     * @throws UndefinedProviderException
     */
    public function getProviderService(?ProviderEnum $provider): ProviderInterface
    {
        return match ($provider) {
            ProviderEnum::VTB => $this->vtbProvider,
            default => throw new UndefinedProviderException(),
        };
    }

    public function random(): ?ProviderEnum
    {
        $probabilities = self::PROBABILITIES;

        while (! empty($probabilities)) {
            $random = random_int(1, 100);
            $cumulativeProbability = 0;

            foreach ($probabilities as $provider => $probability) {
                $cumulativeProbability += $probability;

                if ($random <= $cumulativeProbability) {
                    $providerEnum = ProviderEnum::tryFrom($provider);
                    $providerService = $this->getProviderService($providerEnum);

                    if ($providerService->hasFullConfig()) {
                        return $providerEnum;
                    }

                    unset($probabilities[$provider]);
                    $this->redistributeProbabilities($probabilities);
                    break;
                }
            }
        }

        return null;
    }

    private function redistributeProbabilities(array &$probabilities): void
    {
        if (empty($probabilities)) {
            return;
        }

        $total = array_sum($probabilities);

        foreach ($probabilities as $provider => $probability) {
            $probabilities[$provider] = ($probability / $total) * 100;
        }
    }
}
