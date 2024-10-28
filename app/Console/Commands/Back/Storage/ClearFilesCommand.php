<?php declare(strict_types=1);

namespace App\Console\Commands\Back\Storage;

use Core\Domains\File\FileLocator;
use Core\Domains\File\Models\FileDTO;
use Core\Domains\File\Models\FileSearcher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearFilesCommand extends Command
{
    protected const EXCLUDED = [
        'public/static',
    ];

    protected $signature   = 'storage:clear-unused-files';
    protected $description = 'Удаляет файлы из папок, которых нет в БД';

    public function handle(): void
    {
        $fileService = FileLocator::FileService();

        $searcher = new FileSearcher();
        $files    = $fileService->search($searcher)->getItems()->collect()->map(function (FileDTO $file) {
            return $file->getPath();
        });

        foreach (Storage::directories('public') as $dir) {
            if (in_array($dir, self::EXCLUDED)) {
                continue;
            }
            $this->output->info(Storage::path($dir));

            $paths = Storage::files($dir);
            foreach ($paths as $path) {
                $contains = $files->contains($path);
                $message  = Storage::path($path);
                if ( ! $contains) {
                    Storage::delete($path);
                    $this->error($message);
                }
            }
            if ( ! Storage::files($dir)) {
                Storage::deleteDirectory($dir);
            }
        }
    }
}
