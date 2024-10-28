<?php declare(strict_types=1);

namespace App\Console\Commands\Back\Storage;

use Core\Domains\File\Collections\Files;
use Core\Domains\File\FileLocator;
use Core\Domains\File\Models\FileSearcher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MoveFilesCommand extends Command
{
    protected $signature   = 'storage:move-files';
    protected $description = 'Удаляет файлы из папок, которых нет в БД';

    public function handle(): void
    {
        $searcher = new FileSearcher();
        $files    = FileLocator::FileService()->search($searcher)->getItems();

        foreach (Storage::directories('public') as $dir) {
            $this->processDir($dir, $files);
        }
    }

    private function processDir(string $dir, Files $files, bool $subDir = false): void
    {
        foreach (Storage::directories($dir) as $d) {
            $this->processDir($d, $files, true);
        }

        $this->output->info(Storage::path($dir));

        $paths = Storage::files($dir);

        foreach ($paths as $path) {
            $file    = $files->findByPath($path);
            $message = Storage::path($path);

            if ($file) {
                $newDir      = $subDir ? dirname($dir) : $dir;
                $newFileName = FileLocator::FileService()->generateName(storage_path($newDir), $file->getExt());
                $newPath     = sprintf('%s/%s', $newDir, $newFileName);

                FileLocator::FileService()->move($file, $newPath);

                $this->error(Storage::path($path) . ' => ' . sprintf('%s/%s', storage_path($newDir), $newFileName));
            }
            else {
                $this->info($message);
            }
        }

        if ( ! Storage::files($dir) && ! Storage::directories($dir)) {
            Storage::deleteDirectory($dir);
        }
    }
}
