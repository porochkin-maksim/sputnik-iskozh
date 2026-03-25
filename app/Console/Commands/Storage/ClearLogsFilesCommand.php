<?php declare(strict_types=1);

namespace App\Console\Commands\Storage;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class ClearLogsFilesCommand extends Command
{
    protected $signature = 'storage:logs:clear-unused-files
                            {--days=7 : Удалять файлы старше указанного количества дней}
                            {--path=errors : Директория относительно storage/logs (можно указать полный путь)}
                            {--dry-run : Только показать, что будет удалено, без реального удаления}
                            {--force : Выполнить удаление без подтверждения}';

    protected $description = 'Удаляет старые файлы логов из указанной директории';

    public function handle(): int
    {
        $days   = (int) $this->option('days');
        $path   = $this->option('path');
        $dryRun = $this->option('dry-run');
        $force  = $this->option('force');

        // Определяем полный путь
        if (str_starts_with($path, DIRECTORY_SEPARATOR) || str_contains($path, ':')) {
            $directory = $path;
        }
        else {
            $directory = storage_path('logs/' . ltrim($path, DIRECTORY_SEPARATOR));
        }

        if ( ! is_dir($directory)) {
            $this->error("Директория не существует: {$directory}");

            return self::FAILURE;
        }

        $threshold     = Carbon::now()->subDays($days);
        $files         = File::files($directory);
        $filesToDelete = [];

        foreach ($files as $file) {
            if ( ! $file->isFile()) {
                continue;
            }

            $lastModified = Carbon::createFromTimestamp($file->getMTime());
            if ($lastModified->lt($threshold)) {
                $filesToDelete[] = $file;
            }
        }

        if (empty($filesToDelete)) {
            $this->info("Нет файлов логов старше {$days} дней в {$directory}");

            return self::SUCCESS;
        }

        $this->info("Найдено файлов для удаления: " . count($filesToDelete));

        if ($dryRun) {
            foreach ($filesToDelete as $file) {
                $this->line("  - {$file->getFilename()} (изменён: " . $file->getMTime()->format('Y-m-d H:i:s') . ")");
            }
            $this->warn('DRY-RUN: Никакие файлы не были удалены.');

            return self::SUCCESS;
        }

        if ( ! $force && ! $this->confirm("Удалить " . count($filesToDelete) . " файлов?")) {
            $this->info('Операция отменена.');

            return self::SUCCESS;
        }

        $deleted = 0;
        foreach ($filesToDelete as $file) {
            try {
                if (File::delete($file->getPathname())) {
                    $deleted++;
                    $this->line("✅ Удалён: {$file->getFilename()}");
                }
                else {
                    $this->error("❌ Не удалось удалить: {$file->getFilename()}");
                }
            }
            catch (\Throwable $e) {
                $this->error("❌ Ошибка при удалении {$file->getFilename()}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("Удалено файлов: {$deleted}");

        return self::SUCCESS;
    }
}