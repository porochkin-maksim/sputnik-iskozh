<?php

namespace App\Providers;

use Core\LangEnum;
use Core\Translations\TranslationService;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    private TranslationService $translationLogService;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->translationLogService = new TranslationService();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->setLocale(LangEnum::RU);

        Lang::handleMissingKeysUsing(function (string $key, array $replacements, string $locale,) {
            $this->translationLogService->log($key);
            return $key;
        });
    }
}
