<?php declare(strict_types=1);

namespace Core\Domains\Infra\Uid;

readonly class UidDTO
{
    public function __construct(
        private string      $token,
        private UidTypeEnum $type,
        private ?int        $referenceId,
    )
    {
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getType(): UidTypeEnum
    {
        return $this->type;
    }

    public function getReferenceId(): ?int
    {
        return $this->referenceId;
    }
}
