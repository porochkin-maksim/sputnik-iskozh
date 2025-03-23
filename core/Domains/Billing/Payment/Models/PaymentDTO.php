<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment\Models;

use Core\Domains\Account\Models\AccountDTO;
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Domains\File\Models\FileDTO;

class PaymentDTO
{
    use TimestampsTrait;

    private ?int    $id         = null;
    private ?int    $invoice_id = null;
    private ?int    $account_id = null;
    private ?float  $cost       = null;
    private ?bool   $moderated  = null;
    private ?bool   $verified   = null;
    private ?string $comment    = null;

    private ?InvoiceDTO $invoice = null;
    private ?AccountDTO $account = null;

    /**
     * @var FileDTO[]
     */
    private array $files = [];

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
        return $this->invoice_id;
    }

    public function setInvoiceId(?int $invoice_id): static
    {
        $this->invoice_id = $invoice_id;

        return $this;
    }

    public function getAccountId(): ?int
    {
        return $this->account_id;
    }

    public function setAccountId(?int $account_id): static
    {
        $this->account_id = $account_id;

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

    public function getInvoice(): ?InvoiceDTO
    {
        return $this->invoice;
    }

    public function setInvoice(?InvoiceDTO $invoice): static
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getAccount(): ?AccountDTO
    {
        return $this->account;
    }

    public function setAccount(?AccountDTO $account): static
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @param FileDTO[] $files
     */
    public function setFiles(array $files): static
    {
        $this->files = $files;

        return $this;
    }

    public function getFiles(): array
    {
        return $this->files;
    }
}
