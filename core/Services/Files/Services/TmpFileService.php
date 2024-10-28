<?php declare(strict_types=1);

namespace Core\Services\Files\Services;

use Core\Services\Files\Factories\MetaDataFactory;
use Core\Services\Files\Jobs\RemoveFileJob;
use Core\Services\Files\Models\MetaData;
use Illuminate\Support\Str;

readonly class TmpFileService
{
    public function __construct(
        private MetaDataFactory $metaDataFactory,
    )
    {
    }

    public function createTmpFile(string $ext = 'tmp', string $content = ''): string
    {
        $path = $this->getPath(Str::random(40) . '.' . $ext);
        $file = fopen($path, 'w');
        fwrite($file, $content);
        fclose($file);

        return $path;
    }

    public function getMetaData(string $filePath): ?MetaData
    {
        try {
            $file = fopen($filePath, 'r');
            $this->metaDataFactory->make($file);
            fclose($file);
        }
        catch (\Throwable) {
            return null;
        }
    }

    public function deleteTmpFile(string $path): RemoveFileJob
    {
        return new RemoveFileJob($path);
    }

    private function getPath(string $fileName): string
    {
        $storagePath = storage_path('tmp' . DIRECTORY_SEPARATOR);

        if ( ! file_exists($storagePath)) {
            mkdir($storagePath, 0777, true);
        }

        return $storagePath . $fileName;
    }
}
