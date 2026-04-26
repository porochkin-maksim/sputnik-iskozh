<?php declare(strict_types=1);

namespace Core\Domains\Account;

use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
use Core\Domains\Infra\ExData\Models\AbstractExData;

class AccountExDataEntity extends AbstractExData
{
    private ?string $cadastreNumber;

    public function __construct(array $data = [])
    {
        $this->cadastreNumber = $data['cadastreNumber'] ?? null;
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

    public function jsonSerialize(): array
    {
        return [
            'cadastreNumber' => $this->cadastreNumber,
        ];
    }
}
