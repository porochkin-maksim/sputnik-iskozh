<?php

namespace App\Models\Billing;

use App\Models\Interfaces\CastsInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property string  $name
 * @property int     $type
 * @property int     $period_id
 * @property float   $cost
 * @property bool    $active
 */
class Service extends Model implements CastsInterface
{
    use SoftDeletes;

    public const TABLE = 'services';

    protected $table = self::TABLE;

    public const ID        = 'id';
    public const TYPE      = 'type';
    public const PERIOD_ID = 'period_id';
    public const NAME      = 'name';
    public const COST      = 'cost';
    public const ACTIVE    = 'active';

    protected $guarded = [];

    protected $casts = [
        self::COST   => self::CAST_FLOAT,
        self::ACTIVE => self::CAST_BOOLEAN,
    ];
}
