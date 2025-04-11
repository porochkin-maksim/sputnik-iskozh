<?php declare(strict_types=1);

namespace Core\Domains\Account\Models;

use Carbon\Carbon;
use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
use Core\Domains\Infra\ExData\Models\AbstractExData;
use Core\Enums\DateTimeFormat;
use Core\Helpers\DateTime\DateTimeHelper;

class AccountExDataDTO extends AbstractExData
{
    private ?string $cadastreNumber;
    private ?Carbon $registryDate;

    public function __construct(array $data = [])
    {
        $this->cadastreNumber = $data['cadastreNumber'] ?? null;
        $this->registryDate   = DateTimeHelper::toCarbonOrNull($data['registryDate'] ?? null);
    }

    public function getType(): ExDataTypeEnum
    {
        return ExDataTypeEnum::ACCOUNT;
    }

    public function getCadastreNumber(): ?string
    {
        return $this->cadastreNumber;
    }

    public function setCadastreNumber(?string $cadastreNumber): static
    {
        $this->cadastreNumber = $cadastreNumber;

        return $this;
    }

    public function getRegistryDate(): ?Carbon
    {
        return $this->registryDate;
    }

    public function setRegistryDate(?Carbon $registryDate): static
    {
        $this->registryDate = $registryDate;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'cadastreNumber' => $this->cadastreNumber,
            'registryDate'   => $this->registryDate?->format(DateTimeFormat::DATE_DEFAULT),
        ];
    }
} 