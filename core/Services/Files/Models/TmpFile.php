<?php declare(strict_types=1);

namespace Core\Services\Files\Models;

readonly class TmpFile
{
    public function __construct(
        private string $originalName,
        private string $path,
        private string $ext = 'tmp',
    )
    {
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getExt(): string
    {
        return $this->ext;
    }
}
