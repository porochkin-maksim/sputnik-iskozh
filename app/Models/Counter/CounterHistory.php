<?php declare(strict_types=1);

namespace App\Models\Counter;

use App\Models\AbstractModel;
use App\Models\Billing\ClaimToObject;
use App\Models\Files\FileModel;
use Carbon\Carbon;
use Core\Domains\Billing\ClaimToObject\ClaimObjectTypeEnum;
use Core\Domains\Files\FileTypeEnum;
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
 * @property ?float  $previous_value
 * @property ?Carbon $date
 * @property ?bool   $is_verified
 */
class CounterHistory extends AbstractModel
{
    public const string TABLE = 'counter_history';

    protected $table = self::TABLE;

    public const string ID             = 'id';
    public const string COUNTER_ID     = 'counter_id';
    public const string PREVIOUS_ID    = 'previous_id';
    public const string PREVIOUS_VALUE = 'previous_value';
    public const string VALUE          = 'value';
    public const string DATE           = 'date';
    public const string IS_VERIFIED    = 'is_verified';

    public const string RELATION_FILE     = 'file';
    public const string RELATION_COUNTER  = 'counter';
    public const string RELATION_PREVIOUS = 'previous';
    public const string RELATION_CLAIM    = 'claim';

    protected $guarded = [];
    protected $with    = [self::RELATION_FILE, self::RELATION_PREVIOUS];

    protected $casts = [
        self::COUNTER_ID     => self::CAST_INTEGER,
        self::PREVIOUS_ID    => self::CAST_INTEGER,
        self::VALUE          => self::CAST_FLOAT,
        self::PREVIOUS_VALUE => self::CAST_FLOAT,
        self::DATE           => self::CAST_DATETIME,
        self::IS_VERIFIED    => self::CAST_BOOLEAN,
    ];

    public const string TITLE_COUNTER_ID  = 'Счётчик';
    public const string TITLE_VALUE       = 'Показание';
    public const string TITLE_DATE        = 'Дата показания';
    public const string TITLE_IS_VERIFIED = 'Подтверждён';

    public const array PROPERTIES_TO_TITLES = [
        self::COUNTER_ID  => self::TITLE_COUNTER_ID,
        self::VALUE       => self::TITLE_VALUE,
        self::DATE        => self::TITLE_DATE,
        self::IS_VERIFIED => self::TITLE_IS_VERIFIED,
    ];

    public function file(): HasOne
    {
        return $this->hasOne(FileModel::class, FileModel::RELATED_ID)
            ->where(FileModel::TYPE, FileTypeEnum::COUNTER_HISTORY->value)
        ;
    }

    public function counter(): BelongsTo
    {
        return $this->belongsTo(Counter::class, self::COUNTER_ID)
            ->with(Counter::RELATION_ACCOUNT)
            ->without(Counter::RELATION_HISTORY)
        ;
    }

    public function previous(): HasOne
    {
        return $this->hasOne(self::class, self::ID, self::PREVIOUS_ID);
    }

    public function claim(): HasOne
    {
        return $this->hasOne(ClaimToObject::class, ClaimToObject::REFERENCE_ID, self::ID)
            ->where(ClaimToObject::TYPE, ClaimObjectTypeEnum::COUNTER_HISTORY->value)
        ;
    }
}
