<?php declare(strict_types=1);

namespace Core\Translations;

use Core\Log\LogChannel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    private string $logPath = '';

    public function __construct()
    {
        $config = config('logging')['channels'][LogChannel::TRANSLATIONS];

        $this->logPath = $config['path'];
    }

    public function log(string $key): void
    {
        $key = trim($key);
        if (!exec(sprintf('grep %s %s', $key, $this->logPath))) {
            Log::channel(LogChannel::TRANSLATIONS)->debug(sprintf('"%s": ""', $key));
        }
    }

    public function sortTranslations(): void
    {
        $locale     = Lang::locale();
        $localePath = resource_path(sprintf('lang/%s.json', $locale));
        if (!File::exists($localePath)) {
            return;
        }

        $file    = File::get($localePath);
        $content = array_filter(explode("\n", $file), fn($string) => !in_array($string, ['{', '}', '']));
        sort($content);

        $content = collect($content)->map(function ($string, int $key) use ($content) {
                $ending = $key === count($content) - 1 ? '' : ',';
                return str_replace('",', '"', $string) . $ending;
            })->toArray()
        ;

        $result = array_merge(['{'], $content, ['}']);
        File::put($localePath, implode("\n", $result));
    }
}
