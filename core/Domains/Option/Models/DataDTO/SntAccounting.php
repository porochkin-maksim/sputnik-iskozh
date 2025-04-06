<?php declare(strict_types=1);

namespace Core\Domains\Option\Models\DataDTO;

class SntAccounting implements DataDTOInterface
{
    private ?string $bank  = null;
    private ?string $acc   = null;
    private ?string $corr  = null;
    private ?string $bik   = null;
    private ?string $inn   = null;
    private ?string $kpp   = null;
    private ?string $ogrn  = null;

    public function getBank(): ?string
    {
        return $this->bank;
    }

    public function setBank(?string $bank): static
    {
        $this->bank = $bank;

        return $this;
    }

    public function getAcc(): ?string
    {
        return $this->acc;
    }

    public function setAcc(?string $acc): static
    {
        $this->acc = $acc;

        return $this;
    }

    public function getCorr(): ?string
    {
        return $this->corr;
    }

    public function setCorr(?string $corr): static
    {
        $this->corr = $corr;

        return $this;
    }

    public function getBik(): ?string
    {
        return $this->bik;
    }

    public function setBik(?string $bik): static
    {
        $this->bik = $bik;

        return $this;
    }

    public function getInn(): ?string
    {
        return $this->inn;
    }

    public function setInn(?string $inn): static
    {
        $this->inn = $inn;

        return $this;
    }

    public function getKpp(): ?string
    {
        return $this->kpp;
    }

    public function setKpp(?string $kpp): static
    {
        $this->kpp = $kpp;

        return $this;
    }

    public function getOgrn(): ?string
    {
        return $this->ogrn;
    }

    public function setOgrn(?string $ogrn): static
    {
        $this->ogrn = $ogrn;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'bank' => $this->getBank(),
            'acc'  => $this->getAcc(),
            'corr' => $this->getCorr(),
            'bik'  => $this->getBik(),
            'inn'  => $this->getInn(),
            'kpp'  => $this->getKpp(),
            'ogrn' => $this->getOgrn(),
        ];
    }
} 