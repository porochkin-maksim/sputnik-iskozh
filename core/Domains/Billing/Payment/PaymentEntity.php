<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment;

use Carbon\Carbon;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\Files\FileCollection;
use Core\Shared\Helpers\DateTime\DateTimeHelper;

class PaymentEntity
{
    use TimestampsTrait;

    private ?int $id = null;
    private ?int $invoiceId = null;
    private ?int $accountId = null;
    private ?float $cost = null;
    private ?bool $moderated = null;
    private ?bool $verified = null;
    private ?string $comment = null;
    private ?string $name = null;
    private ?array $data = null;
    private ?Carbon $paidAt = null;
    private ?string $accountNumber = null;
    private ?InvoiceEntity $invoice = null;
    private ?AccountEntity $account = null;
    private ?FileCollection $files = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getInvoiceId(): ?int
    {
        return $this->invoiceId;
    }

    public function setInvoiceId(?int $invoiceId): static
    {
        $this->invoiceId = $invoiceId;

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

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(?float $cost): static
    {
        $this->cost = $cost;

        return $this;
    }

    public function isModerated(): ?bool
    {
        return $this->moderated;
    }

    public function setModerated(?bool $moderated): static
    {
        $this->moderated = $moderated;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->verified;
    }

    public function setVerified(?bool $verified): static
    {
        $this->verified = $verified;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getPaidAt(): ?Carbon
    {
        return $this->paidAt;
    }

    public function setPaidAt(mixed $paidAt): static
    {
        $this->paidAt = DateTimeHelper::toCarbonOrNull($paidAt);

        return $this;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(?string $accountNumber): static
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    public function getInvoice(): ?InvoiceEntity
    {
        return $this->invoice;
    }

    public function setInvoice(?InvoiceEntity $invoice): static
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getAccount(): ?AccountEntity
    {
        return $this->account;
    }

    public function setAccount(?AccountEntity $account): static
    {
        $this->account = $account;

        return $this;
    }

    public function getFiles(): FileCollection
    {
        return $this->files ?: new FileCollection();
    }

    public function setFiles(FileCollection $files): static
    {
        $this->files = $files;

        return $this;
    }
}
