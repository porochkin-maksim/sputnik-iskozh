<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim;

use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Service\ServiceEntity;
use Core\Domains\Common\Traits\TimestampsTrait;

class ClaimEntity
{
    use TimestampsTrait;

    private ?int           $id        = null;
    private ?int           $invoiceId = null;
    private ?int           $serviceId = null;
    private ?string        $name      = null;
    private ?float         $tariff    = null;
    private ?float         $cost      = null;
    private ?float         $paid      = null;
    private ?ServiceEntity $service   = null;
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

    public function getServiceId(): ?int
    {
        return $this->serviceId;
    }

    public function setServiceId(?int $serviceId): static
    {
        $this->serviceId = $serviceId;

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

    public function getPaid(): ?float
    {
        return $this->paid;
    }

    public function setPaid(?float $paid): static
    {
        $this->paid = $paid;

        return $this;
    }

    public function getDelta(): ?float
    {
        if ($this->getCost() === null || $this->getPaid() === null) {
            return null;
        }

        return $this->getCost() - $this->getPaid();
    }

    public function getService(): ?ServiceEntity
    {
        return $this->service;
    }

    public function setService(?ServiceEntity $service): static
    {
        $this->service = $service;

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
}
