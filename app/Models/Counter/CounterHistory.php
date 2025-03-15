<?php

namespace App\Models\Counter;

use App\Models\File\File;
use App\Models\Interfaces\CastsInterface;
use Carbon\Carbon;
use Core\Domains\File\Enums\TypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property int     $counter_id
 * @property ?float  $value
 * @property ?Carbon $date
 * @property ?bool   $is_verified
 */
class CounterHistory extends Model implements CastsInterface
{
    public const TABLE = 'counter_history';

    protected $table = self::TABLE;

    public const ID          = 'id';
    public const COUNTER_ID  = 'counter_id';
    public const VALUE       = 'value';
    public const DATE        = 'date';
    public const IS_VERIFIED = 'is_verified';

    public const FILE    = 'file';
    public const COUNTER = 'counter';

    protected $guarded = [];
    protected $with    = [self::FILE];

    protected $casts = [
        self::COUNTER_ID  => self::CAST_INTEGER,
        self::VALUE       => self::CAST_FLOAT,
        self::DATE        => self::CAST_DATETIME,
        self::IS_VERIFIED => self::CAST_BOOLEAN,
    ];

    public function file(): HasOne
    {
        return $this->hasOne(File::class, File::RELATED_ID)
            ->where(File::TYPE, TypeEnum::COUNTER->value)
        ;
    }

    public function counter(): BelongsTo
    {
        return $this->belongsTo(Counter::class, self::COUNTER_ID)
            ->with(Counter::ACCOUNT)
            ->without(Counter::HISTORY);
    }
}
