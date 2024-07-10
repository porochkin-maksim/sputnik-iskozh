<?php declare(strict_types=1);

namespace Core\Services\Images\Factories;

use Core\Services\Images\Enums\StaticFileName;
use Core\Services\Images\Models\StaticFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class StaticFileFactory
{
    public function make(StaticFileName $path): ?StaticFile
    {
        $localPath = 'public/static/' . $path->value;
        if (Storage::exists($localPath)) {
            $ext = File::extension($localPath);

            return new StaticFile(
                $path->value,
                $ext,
                $localPath,
                Storage::url($localPath),
            );
        }

        return null;
    }
}
