<?php declare(strict_types=1);

namespace Core\Services\External\VTB\Responses;

use Carbon\Carbon;

abstract class BaseResponse implements \JsonSerializable
{
    private const string CREATED_AT = 'createdAt';

    private string $createdAt;

    public function __construct(array $data)
    {
        $this->createdAt = ($data[self::CREATED_AT] ?? null) ?: Carbon::now()->toDateTimeString();
    }

    public function getCreatedAt(): Carbon
    {
        return Carbon::parse($this->createdAt);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            self::CREATED_AT => $this->createdAt,
        ];
    }
}
