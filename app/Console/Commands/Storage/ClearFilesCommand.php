<?php declare(strict_types=1);

namespace App\Console\Commands\Storage;

use App\Models\File\File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use function Laravel\Prompts\confirm;

class ClearFilesCommand extends Command
{
    protected $signature   = 'storage:clear-unused-files {--delete : Реально удалить файлы}';
    protected $description = 'Удаляет файлы из storage/app/public, которых нет в БД (кроме папки static)';

    protected const EXCLUDED = [
        'static',
        '.gitignore',
    ];

    public function handle(): void
    {
        $delete = $this->option('delete');
        $dryRun = ! $delete;

        if ($dryRun) {
            $this->warn('Режим просмотра: файлы не будут удалены. Для реального удаления используйте --delete');
        }
        else {
            if ( ! confirm('Вы уверены, что хотите удалить файлы, отсутствующие в БД?', false)) {
                $this->info('Операция отменена');

                return;
            }
        }

        $disk    = Storage::disk('public');
        $dbFiles = File::all()
            ->map(fn($file) => $file->path) // полный путь с public/
            ->map(fn($path) => substr($path, 7)) // удаляем 'public/'
            ->filter()                           // убираем пустые
            ->toArray()
        ;

        $this->info('Всего файлов в БД: ' . count($dbFiles));

        $rootDirectories   = $disk->directories('/');
        $totalDeletedFiles = 0;
        $totalDeletedDirs  = 0;

        foreach ($rootDirectories as $dir) {
            if (in_array($dir, self::EXCLUDED)) {
                $this->line("Пропуск каталога: {$dir}");
                continue;
            }
            $result            = $this->processDirectory($disk, $dir, $dbFiles, $dryRun);
            $totalDeletedFiles += $result['files'];
            $totalDeletedDirs  += $result['dirs'];
        }

        $this->info("Удалено файлов: {$totalDeletedFiles}, удалено каталогов: {$totalDeletedDirs}");

        if ($dryRun) {
            $this->warn('Режим просмотра: реального удаления не было');
        }
        else {
            $this->info('Очистка завершена');
        }
    }

    private function processDirectory($disk, string $dir, array $dbFiles, bool $dryRun): array
    {
        $deletedFiles = 0;
        $deletedDirs  = 0;

        // Обрабатываем подкаталоги рекурсивно
        foreach ($disk->directories($dir) as $subDir) {
            if (in_array($subDir, self::EXCLUDED)) {
                $this->line("Пропуск подкаталога: {$subDir}");
                continue;
            }
            $result       = $this->processDirectory($disk, $subDir, $dbFiles, $dryRun);
            $deletedFiles += $result['files'];
            $deletedDirs  += $result['dirs'];
        }

        // Проверяем файлы в текущем каталоге
        $files = $disk->files($dir);
        foreach ($files as $file) {
            if (in_array($file, self::EXCLUDED)) {
                continue;
            }
            if ( ! in_array($file, $dbFiles)) {
                $this->line("Удаление файла: {$file}");
                if ( ! $dryRun) {
                    $disk->delete($file);
                }
                $deletedFiles++;
            }
        }

        // Если каталог пуст (нет файлов и подкаталогов), удаляем его
        if ($disk->allFiles($dir) === [] && $disk->allDirectories($dir) === []) {
            $this->line("Удаление каталога: {$dir}");
            if ( ! $dryRun) {
                $disk->deleteDirectory($dir);
            }
            $deletedDirs++;
        }

        return ['files' => $deletedFiles, 'dirs' => $deletedDirs];
    }
}