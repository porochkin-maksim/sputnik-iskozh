<?php

namespace App\Models\Counter;

use App\Models\Interfaces\CastsInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property int     $counter_id
 * @property ?float  $old_value
 * @property ?float  $new_value
 * @property ?float  $tariff
 */
class CounterHistory extends Model implements CastsInterface
{
    public const TABLE = 'counter_history';

    protected $table = self::TABLE;

    public const ID         = 'id';
    public const COUNTER_ID = 'counter_id';
    public const VALUE      = 'value';
    public const TARIFF     = 'tariff';
    public const COST       = 'cost';

    protected $guarded = [];

    protected $casts = [
        self::COUNTER_ID => self::CAST_INTEGER,
        self::VALUE      => self::CAST_FLOAT,
        self::TARIFF     => self::CAST_FLOAT,
        self::COST       => self::CAST_FLOAT,
    ];
}
