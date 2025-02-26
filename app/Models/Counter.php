<?php

namespace App\Models;

use App\Models\Interfaces\CastsInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property int     $type
 * @property int     $account_id
 * @property string  $number
 * @property bool    $is_primary
 */
class Counter extends Model implements CastsInterface
{
    public const TABLE = 'counters';

    protected $table = self::TABLE;

    public const ID         = 'id';
    public const TYPE       = 'type';
    public const ACCOUNT_ID = 'account_id';
    public const NUMBER     = 'number';
    public const IS_PRIMARY = 'is_primary';

    protected $guarded = [];

    protected $casts = [
        self::IS_PRIMARY => self::CAST_BOOLEAN,
    ];
}
