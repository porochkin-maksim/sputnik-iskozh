<?php declare(strict_types=1);

namespace Core\Domains\Shared\ValueObjects;

readonly class UploadedFile
{
    public function __construct(
        private string $name,
        private string $path,
        private string $mimeType,
        private int    $size,
        private string $content,
    )
    {
    }

    public function getName(): string { return $this->name; }

    public function getPath(): string { return $this->path; }

    public function getMimeType(): string { return $this->mimeType; }

    public function getSize(): int { return $this->size; }

    public function getContent(): string { return $this->content; }

    public function getExtension(): string
    {
        return pathinfo($this->name, PATHINFO_EXTENSION);
    }
}
