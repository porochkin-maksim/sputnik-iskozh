<?php declare(strict_types=1);

namespace Core\Domains\Files;

use Core\Domains\Common\Traits\TimestampsTrait;

class FileEntity
{
    use TimestampsTrait;

    private ?int          $id        = null;
    private ?FileTypeEnum $type      = null;
    private ?int          $relatedId = null;
    private ?int          $parentId  = null;
    private ?int          $order     = null;
    private ?string       $ext       = null;
    private ?string       $name      = null;
    private ?string       $path      = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): ?FileTypeEnum
    {
        return $this->type;
    }

    public function setType(?FileTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getRelatedId(): ?int
    {
        return $this->relatedId;
    }

    public function setRelatedId(?int $relatedId): static
    {
        $this->relatedId = $relatedId;

        return $this;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setParentId(?int $parentId): static
    {
        $this->parentId = $parentId;

        return $this;
    }

    public function getOrder(): int
    {
        return (int) $this->order;
    }

    public function setOrder(?int $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function getExt(): ?string
    {
        return $this->ext;
    }

    public function setExt(?string $ext): static
    {
        $this->ext = $ext ? mb_strtolower($ext) : null;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getDir(): ?string
    {
        return dirname($this->getPath());
    }

    public function getTrueFileName(bool $withExt = true): ?string
    {
        $result = str_replace($this->getDir(), '', $this->getPath());
        if (!$withExt) {
            $extension = '.' . $this->getExt();
            $result = substr($result, 0, -strlen($extension));
        }
        return str_replace('/', '', $result);
    }

    public function getOnlyName(): string
    {
        return str_replace('.' . $this->getExt(), '', $this->getName());
    }

    public function isImage(): bool
    {
        return in_array($this->ext, [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'bmp',
            'tiff',
            'webp',
            'svg',
        ]);
    }
}
