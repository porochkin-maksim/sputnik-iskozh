<?php

namespace App\Models\Counter;

use App\Models\Billing\TransactionToObject;
use App\Models\File\File;
use App\Models\Interfaces\CastsInterface;
use Carbon\Carbon;
use Core\Domains\Billing\TransactionToObject\Enums\TransactionObjectTypeEnum;
use Core\Domains\File\Enums\FileTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property int     $counter_id
 * @property ?int    $previous_id
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
    public const PREVIOUS_ID = 'previous_id';
    public const VALUE       = 'value';
    public const DATE        = 'date';
    public const IS_VERIFIED = 'is_verified';

    public const FILE       = 'file';
    public const COUNTER    = 'counter';
    public const PREVIOUS   = 'previous';
    public const TRANSACTON = 'transacton';

    protected $guarded = [];
    protected $with    = [self::FILE, self::PREVIOUS];

    protected $casts = [
        self::COUNTER_ID  => self::CAST_INTEGER,
        self::PREVIOUS_ID => self::CAST_INTEGER,
        self::VALUE       => self::CAST_FLOAT,
        self::DATE        => self::CAST_DATETIME,
        self::IS_VERIFIED => self::CAST_BOOLEAN,
    ];

    public function file(): HasOne
    {
        return $this->hasOne(File::class, File::RELATED_ID)
            ->where(File::TYPE, FileTypeEnum::COUNTER->value)
        ;
    }

    public function counter(): BelongsTo
    {
        return $this->belongsTo(Counter::class, self::COUNTER_ID)
            ->with(Counter::ACCOUNT)
            ->without(Counter::HISTORY)
        ;
    }

    public function previous(): HasOne
    {
        return $this->hasOne(self::class, self::ID, self::PREVIOUS_ID);
    }

    public function transacton(): HasOne
    {
        return $this->hasOne(TransactionToObject::class, TransactionToObject::REFERENCE_ID, self::ID)
            ->where(TransactionToObject::TYPE, TransactionObjectTypeEnum::COUNTER_HISTORY->value)
        ;
    }
}
