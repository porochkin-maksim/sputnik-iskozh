<?php declare(strict_types=1);

namespace Core\Translations;

use App\Models\User;
use Core\Log\LogChannel;
use Core\Translations\Notifications\TranslationRequired;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;

class TranslationService
{
    public function __construct()
    {
        $config = config('logging')['channels'][LogChannel::TRANSLATIONS];
    }

    public function log(string $key): void
    {
        $key = trim($key);
        if (str_contains($key, 'validation.values')) {
            return;
        }

        $line = sprintf('"%s": ""', $key);
        if ( ! exec(sprintf('grep %s %s', $line, $this->localePath()))) {
            $content      = $this->getContent();
            $readyContent = $this->getReadyContent();

            $translationExists = false;
            foreach ($readyContent as $item) {
                if (str_contains($item, '"$key"')) {
                    $content[]         = $item;
                    $translationExists = true;
                    break;
                }
            }

            if ( ! $translationExists) {
                $content[] = "    $line";
                $user = new User([User::EMAIL => env('ADMIN_EMAIL')]);
                $user->notify(new TranslationRequired($key));
            }

            $this->sortTranslations($content);
        }
    }

    public function sortTranslations(array $content = []): void
    {
        $content = $content ? : $this->getContent();
        sort($content);

        $content = collect($content)->map(function ($string, int $key) use ($content) {
            $ending = $key === count($content) - 1 ? '' : ',';

            return str_replace('",', '"', $string) . $ending;
        })->toArray();

        $result = array_merge(['{'], $content, ['}']);
        File::put($this->localePath(), implode("\n", $result));
    }

    private function localePath(): string
    {
        return resource_path(sprintf('lang/%s.json', Lang::locale()));
    }

    /**
     * @return string[]
     */
    private function getContent(): array
    {
        if ( ! File::exists($this->localePath())) {
            return [];
        }

        $file = File::get($this->localePath());

        return array_filter(explode("\n", $file), fn($string) => ! in_array($string, ['{', '}', '']));
    }

    /**
     * @return string[]
     */
    private function getReadyContent(): array
    {
        $readyTranslations = resource_path(sprintf('lang/_%s.json', Lang::locale()));
        if ( ! File::exists($readyTranslations)) {
            return [];
        }

        $file = File::get($readyTranslations);

        return array_filter(explode("\n", $file), fn($string) => ! in_array($string, ['{', '}', '']));
    }
}
