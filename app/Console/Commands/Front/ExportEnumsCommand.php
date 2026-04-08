<?php declare(strict_types=1);

namespace App\Console\Commands\Front;

use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Illuminate\Console\Command;

class ExportEnumsCommand extends Command
{
    protected const string OUTPUT_FILE_PATH = 'js/utils/enum.js';

    protected $signature   = 'front:export-enum';
    protected $description = 'Экспортирует указанные enum в файл для фронта';

    public function handle(): void
    {
        $enums = [
            'TicketStatusEnum' => TicketStatusEnum::cases(),
        ];

        $jsContent = "// Автоматически сгенерированный файл. Не редактировать вручную.\n";
        $jsContent .= "// Создан: " . date('Y-m-d H:i:s') . "\n";

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

        // Запись файла
        $path      = resource_path(self::OUTPUT_FILE_PATH);
        $directory = dirname($path);
        if ( ! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $directory));
        }

        file_put_contents($path, $jsContent);
        $this->info('Экспорт enum завершён: ' . $path);
    }
}