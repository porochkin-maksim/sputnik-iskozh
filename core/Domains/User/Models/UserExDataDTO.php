<?php declare(strict_types=1);

namespace Core\Domains\User\Models;

use Carbon\Carbon;
use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
use Core\Domains\Infra\ExData\Models\AbstractExData;
use Core\Enums\DateTimeFormat;
use Core\Helpers\DateTime\DateTimeHelper;

class UserExDataDTO extends AbstractExData
{
    private ?Carbon $ownershipDate;
    private ?string $ownershipDutyInfo;

    public function __construct(array $data = [])
    {
        $this->ownershipDate     = DateTimeHelper::toCarbonOrNull($data['ownershipDate'] ?? null);
        $this->ownershipDutyInfo = $data['ownershipDutyInfo'] ?? null;
    }

    public function getType(): ExDataTypeEnum
    {
        return ExDataTypeEnum::USER;
    }

    public function getOwnershipDate(): ?Carbon
    {
        return $this->ownershipDate;
    }

    public function setOwnershipDate(?Carbon $ownershipDate): static
    {
        $this->ownershipDate = $ownershipDate;

        return $this;
    }

    public function getOwnershipDutyInfo(): ?string
    {
        return $this->ownershipDutyInfo;
    }

    public function setOwnershipDutyInfo(?string $ownershipDutyInfo): static
    {
        $this->ownershipDutyInfo = $ownershipDutyInfo;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'ownershipDate'     => $this->ownershipDate?->format(DateTimeFormat::DATE_DEFAULT),
            'ownershipDutyInfo' => $this->ownershipDutyInfo,
        ];
    }
} 