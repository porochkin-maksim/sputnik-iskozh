<?php declare(strict_types=1);

namespace Core\Domains\File\Models;

use Core\Enums\DateTimeFormat;
use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\File\Enums\TypeEnum;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileDTO implements \JsonSerializable
{
    use TimestampsTrait;

    private ?int      $id         = null;
    private ?TypeEnum $type       = null;
    private ?int      $related_id = null;
    private ?int      $order      = null;
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

    public function getOrder(): int
    {
        return (int) $this->order;
    }

    public function setOrder(?int $order): FileDTO
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
        $this->ext = $ext ? Str::lower($ext) : null;

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
            'url'       => $this->url(),
            'updatedAt' => $this->updatedAt?->format(DateTimeFormat::DATE_DEFAULT),
            'isImage'   => $this->isImage(),
        ];
    }

    public function url(): ?string
    {
        return $this->path ? url(Storage::url($this->path)) : null;
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

    public function getOnlyName(): string
    {
        return Str::replace('.' . $this->getExt(), '', $this->getName());
    }
}
