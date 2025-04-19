<?php

namespace App\Models\Billing;

use App\Models\Interfaces\CastsInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property string  $name
 * @property Carbon  $start_at
 * @property Carbon  $end_at
 * @property boolean $is_closed
 */
class Period extends Model implements CastsInterface
{
    public const TABLE = 'periods';

    protected $table = self::TABLE;

    public const ID        = 'id';
    public const NAME      = 'name';
    public const START_AT  = 'start_at';
    public const END_AT    = 'end_at';
    public const IS_CLOSED = 'is_closed';

    protected $guarded = [];

    protected $casts = [
        self::START_AT  => self::CAST_DATETIME,
        self::END_AT    => self::CAST_DATETIME,
        self::IS_CLOSED => 'boolean',
    ];
}
