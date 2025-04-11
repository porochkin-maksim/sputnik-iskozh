<?php

namespace App\Models\Infra;

use App\Models\Interfaces\CastsInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property string  $type
 * @property int     $reference_id
 * @property array   $data
 */
class ExData extends Model implements CastsInterface
{
    public const TABLE = 'ex_data';

    protected $table = self::TABLE;

    public const ID           = 'id';
    public const TYPE         = 'type';
    public const REFERENCE_ID = 'reference_id';
    public const DATA         = 'data';

    protected $guarded = [];

    protected $casts = [
        self::DATA => self::CAST_JSON,
    ];
}
