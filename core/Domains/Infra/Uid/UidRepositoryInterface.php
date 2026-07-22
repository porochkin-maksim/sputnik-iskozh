<?php declare(strict_types=1);

namespace Core\Domains\Infra\Uid;

interface UidRepositoryInterface
{
    public function getUid(UidTypeEnum $type, int $referenceId): string;

    public function find(string $uid): ?UidDTO;

    public function findReferenceId(string $uid, ?UidTypeEnum $type = null): ?int;

    public function findByReferenceId(UidTypeEnum $type, int $referenceId): ?UidDTO;
}
