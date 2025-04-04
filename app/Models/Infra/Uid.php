<?php declare(strict_types=1);

namespace App\Models\Infra;

use App\Models\Interfaces\CastsInterface;
use Illuminate\Database\Eloquent\Model;

/**
 *
 * @property string $id
 * @property int    $type
 * @property int    $reference_id
 */
class Uid extends Model implements CastsInterface
{
    public const TABLE = 'uids';

    protected $table = self::TABLE;

    protected $keyType      = 'string';
    public    $incrementing = false;
    public    $timestamps   = false;

    public const ID           = 'id';
    public const TYPE         = 'type';
    public const REFERENCE_ID = 'reference_id';

    protected $guarded = [];
}