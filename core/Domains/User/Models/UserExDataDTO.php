<?php declare(strict_types=1);

namespace Core\Domains\User\Models;

use Carbon\Carbon;
use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
use Core\Domains\Infra\ExData\Models\AbstractExData;
use Core\Enums\DateTimeFormat;
use Core\Helpers\DateTime\DateTimeHelper;
use Core\Helpers\Phone\PhoneHelper;

class UserExDataDTO extends AbstractExData
{
    private ?string $phone;
    private ?string $legalAddress;
    private ?string $postAddress;

    public function __construct(array $data = [])
    {
        $this->phone        = $data['phone'] ?? null;
        $this->legalAddress = $data['legalAddress'] ?? null;
        $this->postAddress  = $data['postAddress'] ?? null;
    }

    public function getType(): ExDataTypeEnum
    {
        return ExDataTypeEnum::USER;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getLegalAddress(): ?string
    {
        return $this->legalAddress;
    }

    public function setLegalAddress(?string $legalAddress): static
    {
        $this->legalAddress = $legalAddress;

        return $this;
    }

    public function getPostAddress(): ?string
    {
        return $this->postAddress;
    }

    public function setPostAddress(?string $postAddress): static
    {
        $this->postAddress = $postAddress;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'phone'        => $this->phone,
            'legalAddress' => $this->legalAddress,
            'postAddress'  => $this->postAddress,
        ];
    }
} 