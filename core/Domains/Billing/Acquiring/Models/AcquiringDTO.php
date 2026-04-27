<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Models;

use Core\Domains\Billing\Acquiring\Enums\ProviderEnum;
use Core\Domains\Billing\Acquiring\Enums\StatusEnum;
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Core\Domains\Common\Traits\TimestampsTrait;

class AcquiringDTO
{
    use TimestampsTrait;

    private ?int          $id         = null;
    private ?int          $invoice_id = null;
    private ?int          $user_id    = null;
    private ?int          $payment_id = null;
    private ?float        $amount     = null;
    private ?ProviderEnum $provider   = null;
    private ?StatusEnum   $status     = null;

    private array $data = [];

    private InvoiceDTO $invoice;

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

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(?int $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getPaymentId(): ?int
    {
        return $this->payment_id;
    }

    public function setPaymentId(?int $payment_id): AcquiringDTO
    {
        $this->payment_id = $payment_id;

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

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): AcquiringDTO
    {
        $this->amount = $amount;

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

    public function setInvoice($invoice): static
    {
        $this->invoice    = $invoice;
        $this->invoice_id = $invoice->getId();

        return $this;
    }

    public function getInvoice(): InvoiceDTO
    {
        return $this->invoice;
    }

    public function makeHash(): string
    {
        return md5($this->getId() . $this->getInvoiceId() . $this->getUserId() . $this->getAmount());
    }
}
