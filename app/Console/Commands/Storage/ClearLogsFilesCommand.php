<?php declare(strict_types=1);

namespace App\Console\Commands\Storage;

use Illuminate\Console\Command;
use Carbon\Carbon;

class ClearLogsFilesCommand extends Command
{
    protected $signature   = 'storage:logs:clear-unused-files';
    protected $description = 'Удаляет старые файлы логов';

    public function handle(): int
    {
        $directory = storage_path('logs/errors');
        $deleted   = 0;

        if ( ! is_dir($directory)) {
            $this->info("Директория {$directory} не существует.");

            return self::SUCCESS;
        }

        foreach (scandir($directory, 0) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $path = $directory . DIRECTORY_SEPARATOR . $file;

            if ( ! is_file($path)) {
                continue;
            }

            $lastModified = filemtime($path);

            // старше 7 дней
            $threshold = Carbon::now()->subWeek();
            if ($threshold->greaterThan(Carbon::createFromTimestamp($lastModified)) && @unlink($path)) {
                $deleted++;
            }
        }

        $this->info("Удалено файлов: {$deleted}");

        return self::SUCCESS;
    }
}
