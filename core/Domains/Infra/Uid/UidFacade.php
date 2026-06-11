<?php declare(strict_types=1);

namespace Core\Domains\Infra\Uid;

use App\Models\Infra\Uid;

abstract class UidFacade
{
    public static function getUid(UidTypeEnum $type, int $referenceId): string
    {
        $uid = Uid::where(Uid::TYPE, $type->value)->where(Uid::REFERENCE_ID, $referenceId)->first();

        if ( ! $uid) {
            $uid = Uid::make([
                Uid::ID           => self::uuid(),
                Uid::TYPE         => $type->value,
                Uid::REFERENCE_ID => $referenceId,
            ]);
            $uid->save();
        }

        return $uid->id;
    }

    public static function find(string $uid): ?UidDTO
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

    public static function findReferenceId(string $uid, ?UidTypeEnum $type = null): ?int
    {
        $result = Uid::find($uid);

        if ( ! $result || $result->{Uid::TYPE} !== $type?->value) {
            return null;
        }

        return $result->{Uid::REFERENCE_ID};
    }

    public static function findByReferenceId(UidTypeEnum $type, int $referenceId): ?UidDTO
    {
        $result = Uid::where(Uid::TYPE, $type->value)
            ->where(Uid::REFERENCE_ID, $referenceId)
            ->first();

        if ($result) {
            return new UidDTO(
                $result->{Uid::ID},
                $type,
                $result->{Uid::REFERENCE_ID},
            );
        }

        return null;
    }

    private static function uuid(): string
    {
        $bytes    = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);

        $hex = bin2hex($bytes);

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12),
        );
    }
}
