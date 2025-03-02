<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges\Models;

use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\User\Models\UserDTO;

class HistoryChangesDTO
{
    use TimestampsTrait;

    private ?int         $id             = null;
    private ?HistoryType $type           = null;
    private ?HistoryType $reference_type = null;
    private ?int         $user_id        = null;
    private ?int         $primary_id     = null;
    private ?int         $reference_id   = null;
    private ?LogData     $log            = null;

    private ?UserDTO $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): ?HistoryType
    {
        return $this->type;
    }

    public function setType(?HistoryType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getReferenceType(): ?HistoryType
    {
        return $this->reference_type;
    }

    public function setReferenceType(?HistoryType $type): static
    {
        $this->reference_type = $type;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(?int $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getPrimaryId(): ?int
    {
        return $this->primary_id;
    }

    public function setPrimaryId(?int $primary_id): static
    {
        $this->primary_id = $primary_id;

        return $this;
    }

    public function getReferenceId(): ?int
    {
        return $this->reference_id;
    }

    public function setReferenceId(?int $reference_id): static
    {
        $this->reference_id = $reference_id;

        return $this;
    }

    public function getLog(): ?LogData
    {
        return $this->log;
    }

    public function setLog(?LogData $log): static
    {
        $this->log = $log;

        return $this;
    }

    public function setUser(UserDTO $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?UserDTO
    {
        return $this->user;
    }
}
