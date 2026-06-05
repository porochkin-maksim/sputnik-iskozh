<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring;

use Core\Domains\Billing\Acquiring\Enums\ProviderEnum;
use Core\Domains\Billing\Acquiring\Enums\StatusEnum;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Common\Traits\TimestampsTrait;

class AcquiringEntity
{
    use TimestampsTrait;

    private ?int           $id        = null;
    private ?int           $invoiceId = null;
    private ?int           $userId    = null;
    private ?int           $paymentId = null;
    private ?float         $amount    = null;
    private ?ProviderEnum  $provider  = null;
    private ?StatusEnum    $status    = null;
    private array          $data      = [];
    private ?InvoiceEntity $invoice   = null;

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

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getPaymentId(): ?int
    {
        return $this->paymentId;
    }

    public function setPaymentId(?int $paymentId): static
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getProvider(): ?ProviderEnum
    {
        return $this->provider;
    }

    public function setProvider(?ProviderEnum $provider): static
    {
        $this->provider = $provider;

        return $this;
    }

    public function getStatus(): ?StatusEnum
    {
        return $this->status;
    }

    public function setStatus(?StatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getInvoice(): ?InvoiceEntity
    {
        return $this->invoice;
    }

    public function setInvoice(?InvoiceEntity $invoice): static
    {
        $this->invoice   = $invoice;
        $this->invoiceId = $invoice?->getId();

        return $this;
    }

    public function makeHash(): string
    {
        return md5($this->getId() . $this->getInvoiceId() . $this->getUserId() . $this->getAmount());
    }
}
