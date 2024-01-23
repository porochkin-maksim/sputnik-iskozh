<?php declare(strict_types=1);

namespace Core\Objects\File\Models;

class FileDTO
{
    public ?FileCategory $category   = null;
    public ?int          $related_id = null;
    public ?string       $ext        = null;
    public ?string       $name       = null;
    public ?string       $path       = null;

    public function getCategory(): ?FileCategory
    {
        return $this->category;
    }

    public function setCategory(?FileCategory $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getRelatedId(): ?int
    {
        return $this->related_id;
    }

    public function setRelatedId(?int $related_id): static
    {
        $this->related_id = $related_id;

        return $this;
    }

    public function getExt(): ?string
    {
        return $this->ext;
    }

    public function setExt(?string $ext): static
    {
        $this->ext = $ext;

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
}
