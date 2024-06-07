<?php declare(strict_types=1);

namespace Core\Objects\File\Models;

use Core\Enums\DateTimeFormat;
use Core\Objects\Common\Traits\TimestampsTrait;
use Core\Objects\File\Enums\TypeEnum;
use Illuminate\Support\Facades\Storage;

class FileDTO implements \JsonSerializable
{
    use TimestampsTrait;

    private ?int      $id         = null;
    private ?TypeEnum $type       = null;
    private ?int      $related_id = null;
    private ?string   $ext        = null;
    private ?string   $name       = null;
    private ?string   $path       = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): FileDTO
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): ?TypeEnum
    {
        return $this->type;
    }

    public function setType(?TypeEnum $type): static
    {
        $this->type = $type;

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

    public function jsonSerialize(): array
    {
        $dossier = new Dossier($this);

        return [
            'dossier'   => $dossier,
            'id'        => $this->id,
            'name'      => $this->name,
            'ext'       => $this->ext,
            'url'       => Storage::url($this->path),
            'updatedAt' => $this->updatedAt?->format(DateTimeFormat::DATE_DEFAULT),
        ];
    }
}
