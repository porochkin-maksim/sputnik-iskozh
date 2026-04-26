<?php declare(strict_types=1);

namespace App\Helpers;

use Core\Domains\Shared\Contracts\FileStorageInterface;
use Illuminate\Support\Facades\Storage;

class FileStorage implements FileStorageInterface
{
    public function put(string $path, string $content, bool $public = false): bool
    {
        $options = $public ? ['visibility' => 'public'] : [];

        return Storage::put($path, $content, $options);
    }

    public function get(string $path): ?string
    {
        return Storage::exists($path) ? Storage::get($path) : null;
    }

    public function delete(string $path): bool
    {
        return Storage::delete($path);
    }

    public function exists(string $path): bool
    {
        return Storage::exists($path);
    }

    public function copy(string $from, string $to): bool
    {
        return Storage::copy($from, $to);
    }

    public function getVisibility(string $path): string
    {
        return Storage::getVisibility($path);
    }

    public function setVisibility(string $path, string $visibility): bool
    {
        return Storage::setVisibility($path, $visibility);
    }
}
