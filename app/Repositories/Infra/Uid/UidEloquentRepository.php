<?php declare(strict_types=1);

namespace App\Repositories\Infra\Uid;

use App\Models\Infra\Uid;
use Core\Contracts\StringServiceInterface;
use Core\Domains\Infra\Uid\UidDTO;
use Core\Domains\Infra\Uid\UidRepositoryInterface;
use Core\Domains\Infra\Uid\UidTypeEnum;

readonly class UidEloquentRepository implements UidRepositoryInterface
{
    public function __construct(
        private StringServiceInterface $stringService,
    )
    {
    }

    public function getUid(UidTypeEnum $type, int $referenceId): string
    {
        $uid = Uid::where(Uid::TYPE, $type->value)->where(Uid::REFERENCE_ID, $referenceId)->first();

        if ( ! $uid) {
            $uid = Uid::make([
                Uid::ID           => $this->stringService->uuid(),
                Uid::TYPE         => $type->value,
                Uid::REFERENCE_ID => $referenceId,
            ]);
            $uid->save();
        }

        return $uid->id;
    }

    public function find(string $uid): ?UidDTO
    {
        $result = Uid::find($uid);

        if ($result) {
            return new UidDTO(
                $result->{Uid::ID},
                UidTypeEnum::tryFrom($result->{Uid::TYPE}),
                $result->{Uid::REFERENCE_ID},
            );
        }

        return null;
    }

    public function findReferenceId(string $uid, ?UidTypeEnum $type = null): ?int
    {
        $result = Uid::find($uid);

        if ( ! $result || $result->{Uid::TYPE} !== $type?->value) {
            return null;
        }

        return $result->{Uid::REFERENCE_ID};
    }

    public function findByReferenceId(UidTypeEnum $type, int $referenceId): ?UidDTO
    {
        $result = Uid::where(Uid::TYPE, $type->value)
            ->where(Uid::REFERENCE_ID, $referenceId)
            ->first()
        ;

        if ($result) {
            return new UidDTO(
                $result->{Uid::ID},
                $type,
                $result->{Uid::REFERENCE_ID},
            );
        }

        return null;
    }
}
