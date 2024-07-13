<?php declare(strict_types=1);

namespace Core\Domains\File\Models;

use Core\Domains\Common\Traits\TimestampsTrait;

class FolderDTO implements \JsonSerializable
{
    use TimestampsTrait;

    private ?int    $id       = null;
    private ?int    $parentId = null;
    private ?string $uid      = null;
    private ?string $name     = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

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

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(?string $uid): static
    {
        $this->uid = $uid;

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

    public function jsonSerialize(): array
    {
        return [
            'id'       => $this->id,
            'uid'      => $this->uid,
            'parentId' => $this->parentId,
            'name'     => $this->name,
        ];
    }
}
