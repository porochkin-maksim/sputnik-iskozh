<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Services;

use Core\Domains\Billing\Acquiring\Enums\ProviderEnum;
use Core\Domains\Billing\Acquiring\Exceptions\UndefinedProviderException;
use Core\Domains\Billing\Acquiring\ProviderLocator;
use Core\Domains\Billing\Acquiring\Providers\ProviderInterface;

class ProviderSelector
{
    private const array PROBABILITIES = [
        // ProviderEnum::VTB->value => 100,
    ];

    /**
     * @throws UndefinedProviderException
     */
    public static function getProviderService(?ProviderEnum $provider): ProviderInterface
    {
        return match ($provider) {
            ProviderEnum::VTB => ProviderLocator::VTB(),
            default           => throw new UndefinedProviderException(),
        };
    }

    /**
     * Возвращает поставщика, который имеет заполненую конфигурацию
     */
    public static function random(): ?ProviderEnum
    {
        // Исходные вероятности
        $probabilities = self::PROBABILITIES;

        while ( ! empty($probabilities)) {
            // Генерируем случайное число от 1 до 100
            $random                = random_int(1, 100);
            $cumulativeProbability = 0;

            // Ищем провайдера по текущим вероятностям
            foreach ($probabilities as $provider => $probability) {
                $cumulativeProbability += $probability;

                if ($random <= $cumulativeProbability) {
                    // Получаем экземпляр провайдера
                    $providerEnum    = ProviderEnum::tryFrom($provider);
                    $providerService = self::getProviderService(ProviderEnum::tryFrom($provider));

                    // Проверяем, есть ли полная конфигурация
                    if ($providerService->hasFullConfig()) {
                        return $providerEnum;
                    }

                    // Если конфигурации нет — удаляем провайдера из списка и перераспределяем вероятности
                    unset($probabilities[$provider]);
                    self::redistributeProbabilities($probabilities);

                    // Выходим из цикла, чтобы начать новый проход с обновлёнными вероятностями
                    break;
                }
            }
        }

        // Если все провайдеры без конфигурации — возвращаем null
        return null;
    }

    /**
     * Перераспределяет вероятности между оставшимися провайдерами
     */
    private static function redistributeProbabilities(array &$probabilities): void
    {
        if (empty($probabilities)) {
            return;
        }

        // Сумма оставшихся вероятностей
        $total = array_sum($probabilities);

        // Нормализуем вероятности к 100%
        foreach ($probabilities as $provider => $probability) {
            $probabilities[$provider] = ($probability / $total) * 100;
        }
    }
}