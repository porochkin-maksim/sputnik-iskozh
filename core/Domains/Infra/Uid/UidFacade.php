<?php declare(strict_types=1);

namespace Core\Domains\Infra\Uid;

use App\Models\Infra\Uid;
use Illuminate\Support\Str;

abstract class UidFacade
{
    public static function getUid(UidTypeEnum $type, int $referenceId): string
    {
        $uid = Uid::where(Uid::TYPE, $type->value)->where(Uid::REFERENCE_ID, $referenceId)->first();

        if ( ! $uid) {
            $uid = Uid::make([
                Uid::ID           => Str::uuid()->serialize(),
                Uid::TYPE         => $type->value,
                Uid::REFERENCE_ID => $referenceId,
            ]);
            $uid->save();
        }

        return $uid->id;
    }

    public static function findReferenceId(string $uid, ?UidTypeEnum $type = null): ?int
    {
        $result = Uid::find($uid);

        if ( ! $result || $result->{Uid::TYPE} !== $type->value) {
            return null;
        }
        
        return $result->{Uid::REFERENCE_ID};
    }
}