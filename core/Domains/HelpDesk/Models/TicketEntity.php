<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Models;

use Carbon\Carbon;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountService;
use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\Files\FileEntity;
use Core\Domains\HelpDesk\Enums\TicketPriorityEnum;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\User\UserEntity;
use Core\Domains\User\UserService;

class TicketEntity
{
    use TimestampsTrait;

    private ?int                $id           = null;
    private ?int                $userId       = null;
    private ?int                $accountId    = null;
    private ?TicketTypeEnum     $type         = null;
    private ?int                $categoryId   = null;
    private ?int                $serviceId    = null;
    private ?TicketPriorityEnum $priority     = null;
    private ?TicketStatusEnum   $status       = null;
    private ?string             $description  = null;
    private ?string             $result       = null;
    private ?string             $contactName  = null;
    private ?string             $contactPhone = null;
    private ?string             $contactEmail = null;
    private ?Carbon             $resolvedAt   = null;

    /** @var TicketCommentEntity[] */
    private array $comments = [];

    /** @var FileEntity[] */
    private array $files = [];
    /** @var FileEntity[] */
    private array $resultFiles = [];

    private ?AccountEntity     $account  = null;
    private ?UserEntity        $user     = null;
    private ?TicketCategoryEntity $category = null;
    private ?TicketServiceEntity  $service  = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

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

    public function getAccountId(): ?int
    {
        return $this->accountId;
    }

    public function setAccountId(?int $accountId): static
    {
        $this->accountId = $accountId;

        return $this;
    }

    public function getType(): ?TicketTypeEnum
    {
        return $this->type;
    }

    public function setType(?TicketTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function setCategoryId(?int $categoryId): static
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function getServiceId(): ?int
    {
        return $this->serviceId;
    }

    public function setServiceId(?int $serviceId): static
    {
        $this->serviceId = $serviceId;

        return $this;
    }

    public function getPriority(): ?TicketPriorityEnum
    {
        return $this->priority;
    }

    public function setPriority(?TicketPriorityEnum $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getStatus(): ?TicketStatusEnum
    {
        return $this->status;
    }

    public function setStatus(?TicketStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(?string $result): static
    {
        $this->result = $result;

        return $this;
    }

    public function getContactName(): ?string
    {
        return $this->contactName;
    }

    public function setContactName(?string $contactName): static
    {
        $this->contactName = $contactName;

        return $this;
    }

    public function getContactPhone(): ?string
    {
        return $this->contactPhone;
    }

    public function setContactPhone(?string $contactPhone): static
    {
        $this->contactPhone = $contactPhone;

        return $this;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(?string $contactEmail): static
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    public function getResolvedAt(): ?Carbon
    {
        return $this->resolvedAt;
    }

    public function setResolvedAt(?Carbon $resolvedAt): static
    {
        $this->resolvedAt = $resolvedAt;

        return $this;
    }

    public function getComments(): array
    {
        return $this->comments;
    }

    public function setComments(array $comments): static
    {
        $this->comments = $comments;

        return $this;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param FileEntity[] $files
     */
    public function setFiles(array $files): static
    {
        $this->files = $files;

        return $this;
    }

    public function getResultFiles(): array
    {
        return $this->resultFiles;
    }

    public function setResultFiles(array $resultFiles): static
    {
        $this->resultFiles = $resultFiles;

        return $this;
    }

    public function getAccount(bool $layLoad = false): ?AccountEntity
    {
        return $this->account;
    }

    public function setAccount(?AccountEntity $account): static
    {
        $this->account = $account;

        return $this;
    }

    public function getUser(bool $layLoad = false): ?UserEntity
    {
        return $this->user;
    }

    public function setUser(?UserEntity $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCategory(): ?TicketCategoryEntity
    {
        return $this->category;
    }

    public function setCategory(?TicketCategoryEntity $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getService(): ?TicketServiceEntity
    {
        return $this->service;
    }

    public function setService(?TicketServiceEntity $service): static
    {
        $this->service = $service;

        return $this;
    }
}
