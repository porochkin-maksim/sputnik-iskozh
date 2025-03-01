<?php declare(strict_types=1);

namespace Core\Domains\Account\Models;

use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\User\Collections\Users;
use Core\Domains\User\Models\UserDTO;
use JsonSerializable;

class AccountDTO implements JsonSerializable
{
    use TimestampsTrait;

    private ?int    $id              = null;
    private ?int    $size            = null;
    private ?string $number          = null;
    private ?int    $primary_user_id = null;
    private ?bool   $is_member       = null;
    private ?bool   $is_manager      = null;
    private ?Users  $users           = null;

    public function __construct()
    {
        $this->users = new Users();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getPrimaryUserId(): ?int
    {
        return $this->primary_user_id;
    }

    public function setPrimaryUserId(?int $primaryUserId): static
    {
        $this->primary_user_id = $primaryUserId;

        return $this;
    }

    public function getIsMember(): bool
    {
        return (bool) $this->is_member;
    }

    public function setIsMember(?bool $isMember): static
    {
        $this->is_member = $isMember;

        return $this;
    }

    public function getIsManager(): bool
    {
        return (bool) $this->is_manager;
    }

    public function setIsManager(?bool $is_manager): static
    {
        $this->is_manager = $is_manager;

        return $this;
    }

    public function addUser(UserDTO $user): static
    {
        if ( ! $this->users) {
            $this->users = new Users();
        }
        $this->users->add($user);

        return $this;
    }

    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function jsonSerialize(): array
    {
        $dossier = new Dossier($this);

        return [
            'dossier'         => $dossier,
            'id'              => $this->id,
            'number'          => $this->number,
            'size'            => $this->size,
            'primary_user_id' => $this->primary_user_id,
            'is_member'       => $this->is_member,
            'is_manager'      => $this->is_manager,
        ];
    }
}
