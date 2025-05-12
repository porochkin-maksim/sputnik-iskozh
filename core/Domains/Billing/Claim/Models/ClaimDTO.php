<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim\Models;

use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Core\Domains\Billing\Service\Models\ServiceDTO;
use Core\Domains\Common\Traits\TimestampsTrait;

class ClaimDTO
{
    use TimestampsTrait;

    private ?int        $id         = null;
    private ?int        $invoice_id = null;
    private ?int        $service_id = null;
    private ?string     $name       = null;
    private ?float      $tariff     = null;
    private ?float      $cost       = null;
    private ?float      $payed      = null;
    private ?ServiceDTO $service    = null;
    private ?InvoiceDTO $invoice    = null;

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

    public function getServiceId(): ?int
    {
        return $this->service_id;
    }

    public function setServiceId(?int $service_id): static
    {
        $this->service_id = $service_id;

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

    public function getTariff(): ?float
    {
        return $this->tariff;
    }

    public function setTariff(?float $tariff): static
    {
        $this->tariff = $tariff;

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

    public function getPayed(): ?float
    {
        return $this->payed;
    }

    public function setPayed(?float $payed): static
    {
        $this->payed = $payed;

        return $this;
    }

    public function getService(): ?ServiceDTO
    {
        return $this->service;
    }

    public function setService(?ServiceDTO $service): static
    {
        $this->service = $service;

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
}
