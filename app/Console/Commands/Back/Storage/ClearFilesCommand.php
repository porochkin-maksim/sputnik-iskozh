<?php declare(strict_types=1);

namespace App\Console\Commands\Back\Storage;

use Core\Domains\File\FileLocator;
use Core\Domains\File\Models\FileDTO;
use Core\Domains\File\Models\FileSearcher;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;

class ClearFilesCommand extends Command
{
    protected const EXCLUDED = [
        'public/static',
        'public/.gitignore',
    ];

    protected $signature   = 'storage:clear-unused-files {--mode=read}';
    protected $description = 'Удаляет файлы из папок, которых нет в БД';

    public function handle(): void
    {
        $writingMode = ! confirm('Вы хотите только посмотреть или сразу удалить ненужные файлы?', true, 'Только посмотреть', 'Удалить');

        if ($writingMode) {
            $this->output->error('Режим удаления');
        }
        else {
            $this->output->success('Режим чтения');
        }


        $fileService = FileLocator::FileService();

        $searcher = new FileSearcher();
        $files    = $fileService->search($searcher)->getItems()->collect()->map(function (FileDTO $file) {
            return $file->getPath();
        });

        foreach (Storage::directories('/') as $dir) {
            $this->processDir($dir, $files, $writingMode);
        }
    }

    private function processDir(string $dir, Collection $files, bool $writingMode): void
    {
        if (in_array($dir, self::EXCLUDED, true)) {
            $this->output->warning('Пропуск: ' . Storage::path($dir));
            return;
        }
        foreach (Storage::directories($dir) as $d) {
            $this->processDir($d, $files, $writingMode);
        }

        $this->output->info('Обход каталога: ' . Storage::path($dir));

        foreach (Storage::files($dir) as $path) {
            $contains = $files->contains($path);
            $message  = Storage::path($path);
            if ( ! $contains && ! in_array($path, self::EXCLUDED, true)) {
                $this->error("Удаление файла: {$message}");
                if ($writingMode) {
                    Storage::delete($path);
                }
            }
        }
        if ( ! Storage::files($dir)) {
            $this->output->error("Удаление каталога {$dir}");
            if ($writingMode) {
                Storage::deleteDirectory($dir);
            }
        }
    }

    protected function fire($msg): void
    {
        $style = new OutputFormatterStyle('red', 'yellow', ['bold', 'blink']);
        $this->output->getFormatter()->setStyle('fire', $style);

        $this->output->writeln('<fire>' . $msg . '</fire>');
    }
}
