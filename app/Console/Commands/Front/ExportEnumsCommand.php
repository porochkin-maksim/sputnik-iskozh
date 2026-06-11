<?php declare(strict_types=1);

namespace App\Console\Commands\Front;

use Core\App\User\PasswordPolicy\PasswordPolicy;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Illuminate\Console\Command;

class ExportEnumsCommand extends Command
{
    protected const string OUTPUT_FILE_PATH = 'js/utils/enum.js';
    protected const string POLICY_FILE_PATH = 'js/utils/passwordPolicy.js';

    protected $signature   = 'front:export-enum';
    protected $description = 'Экспортирует указанные enum в файл для фронта';

    public function handle(): void
    {
        $enums = [
            'TicketStatusEnum' => TicketStatusEnum::cases(),
        ];

        $jsContent = "// Автоматически сгенерированный файл. Не редактировать вручную.\n";

        foreach ($enums as $name => $cases) {
            $jsContent .= "\nexport const {$name} = {\n";
            foreach ($cases as $case) {
                $key       = $case->name;
                $value     = $case->value;
                $label     = $case->name();
                $jsContent .= "    {$key}: { value: {$value}, label: '{$label}' },\n";
            }
            $jsContent .= "};\n\n";
            $jsContent .= "export const {$name}Options = Object.values({$name});\n";
        }

        $this->writeFile(self::OUTPUT_FILE_PATH, $jsContent);
        $this->exportPasswordPolicy();
    }

    private function exportPasswordPolicy(): void
    {
        $minLength = PasswordPolicy::MIN_LENGTH;
        $lowercase = PasswordPolicy::LOWERCASE_PATTERN;
        $uppercase = PasswordPolicy::UPPERCASE_PATTERN;
        $digit     = PasswordPolicy::DIGIT_PATTERN;

        $jsContent = "// Автоматически сгенерированный файл. Не редактировать вручную.\n";
        $jsContent .= "// Источник: " . PasswordPolicy::class . "\n\n";
        $jsContent .= "export const PASSWORD_MIN_LENGTH = {$minLength};\n";
        $jsContent .= "export const PASSWORD_LOWERCASE_PATTERN = {$lowercase};\n";
        $jsContent .= "export const PASSWORD_UPPERCASE_PATTERN = {$uppercase};\n";
        $jsContent .= "export const PASSWORD_DIGIT_PATTERN = {$digit};\n";

        $this->writeFile(self::POLICY_FILE_PATH, $jsContent);
    }

    private function writeFile(string $relativePath, string $content): void
    {
        $path      = resource_path($relativePath);
        $directory = dirname($path);
        if ( ! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $directory));
        }

        file_put_contents($path, $content);
        $this->info('Экспорт завершён: ' . $path);
    }
}