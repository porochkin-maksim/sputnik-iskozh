<?php declare(strict_types=1);

namespace App\Console\Commands\Back\Storage;

use Core\Domains\File\FileLocator;
use Core\Domains\File\Models\FileDTO;
use Core\Domains\File\Models\FileSearcher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Output\OutputInterface;

class ClearFilesCommand extends Command
{
    protected const EXCLUDED = [
        'static',
    ];

    protected $signature   = 'storage:clear-unused-files';
    protected $description = 'Удаляет файлы из папок, которых нет в БД';

    public function handle(): void
    {
        $searcher = new FileSearcher();
        $files    = FileLocator::FileService()->search($searcher)->getItems()->collect()->map(function (FileDTO $file) {
            return $file->getPath();
        });

        $dirs = Storage::disk('public')->directories();
        foreach ($dirs as $dir) {
            if (in_array($dir, self::EXCLUDED)) {
                continue;
            }
            $this->output->info(Storage::disk('public')->path($dir));

            $paths = Storage::disk('public')->files($dir);
            foreach ($paths as $path) {
                $contains = $files->contains('public/' . $path);
                $message = Storage::disk('public')->path($path);
                if ( ! $contains) {
                    Storage::disk('public')->delete($path);
                    $this->error($message);
                }
                else {
                    $this->info($message);
                }
            }
            if ( ! Storage::disk('public')->files($dir)) {
                Storage::disk('public')->deleteDirectory($dir);
            }
        }
    }
}
