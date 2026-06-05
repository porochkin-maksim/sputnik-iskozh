<?php declare(strict_types=1);

namespace Database\Factories\Infra;

use App\Models\Infra\ExData;
use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExDataFactory extends Factory
{
    protected $model = ExData::class;

    public function definition(): array
    {
        return [
            ExData::TYPE         => ExDataTypeEnum::ACCOUNT->value,
            ExData::REFERENCE_ID => 0,
            ExData::DATA         => [],
        ];
    }

    public function account(int $accountId): static
    {
        return $this->state([
            ExData::TYPE         => ExDataTypeEnum::ACCOUNT->value,
            ExData::REFERENCE_ID => $accountId,
            ExData::DATA         => [],
        ]);
    }
}
