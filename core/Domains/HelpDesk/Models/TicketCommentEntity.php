<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Models;

use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\User\UserEntity;

class TicketCommentEntity
{
    use TimestampsTrait;

    private ?int $id = null;
    private ?int $ticketId = null;
    private ?int $userId = null;
    private ?string $comment = null;
    private ?bool $isInternal = null;
    private ?TicketEntity $ticket = null;
    private ?UserEntity $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getTicketId(): ?int
    {
        return $this->ticketId;
    }

    public function setTicketId(?int $ticketId): static
    {
        $this->ticketId = $ticketId;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getIsInternal(): ?bool
    {
        return $this->isInternal;
    }

    public function setIsInternal(?bool $isInternal): static
    {
        $this->isInternal = $isInternal;

        return $this;
    }

    public function getTicket(): ?TicketEntity
    {
        return $this->ticket;
    }

    public function setTicket(?TicketEntity $ticket): static
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    public function setUser(?UserEntity $user): static
    {
        $this->user = $user;

        return $this;
    }
}
