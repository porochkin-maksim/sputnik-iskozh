<?php declare(strict_types=1);

namespace App\Services\Images\Models;

use Illuminate\Support\Facades\Storage;
use JsonSerializable;

readonly class StaticFile implements JsonSerializable
{
    public function __construct(
        private string $name,
        private string $ext,
        private string $path,
        private string $url,
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getExt(): string
    {
        return $this->ext;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getStoragePath(): string
    {
        return Storage::path($this->getPath());
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getBase64(): string
    {
        $path    = $this->getStoragePath();
        $content = file_get_contents($path);

        return 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode($content);
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'ext'  => $this->ext,
            'path' => $this->path,
            'url'  => $this->url,
        ];
    }
}
