<?php declare(strict_types=1);

namespace App\Models\Counter;

use App\Models\Account\Account;
use App\Models\File\File;
use App\Models\Interfaces\CastsInterface;
use Carbon\Carbon;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\File\Enums\FileTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property int     $type
 * @property int     $account_id
 * @property string  $number
 * @property bool    $is_invoicing
 * @property int     $increment
 * @property ?Carbon $expire_at
 */
class Counter extends Model implements CastsInterface
{
    use SoftDeletes;

    public const string TABLE = 'counters';

    protected $table = self::TABLE;

    public const string ID           = 'id';
    public const string TYPE         = 'type';
    public const string ACCOUNT_ID   = 'account_id';
    public const string NUMBER       = 'number';
    public const string IS_INVOICING = 'is_invoicing';
    public const string INCREMENT    = 'increment';
    public const string EXPIRE_AT    = 'expire_at';

    public const string ACCOUNT  = 'account';
    public const string HISTORY  = 'history';
    public const string PASSPORT = 'passport';

    protected $guarded = [];

    protected $casts = [
        self::IS_INVOICING => self::CAST_BOOLEAN,
        self::INCREMENT    => self::CAST_INTEGER,
        self::EXPIRE_AT    => self::CAST_DATETIME,
    ];

    public const string TITLE_TYPE         = 'Тип';
    public const string TITLE_ACCOUNT_ID   = 'Участок';
    public const string TITLE_NUMBER       = 'Номер';
    public const string TITLE_IS_INVOICING = 'Выставлять счета';
    public const string TITLE_INCREMENT    = 'Автоприращение (кВт)';

    public const array PROPERTIES_TO_TITLES = [
        self::TYPE         => self::TITLE_TYPE,
        self::ACCOUNT_ID   => self::TITLE_ACCOUNT_ID,
        self::NUMBER       => self::TITLE_NUMBER,
        self::IS_INVOICING => self::TITLE_IS_INVOICING,
        self::INCREMENT    => self::TITLE_INCREMENT,
    ];

    public function history(): HasMany
    {
        return $this->hasMany(CounterHistory::class, CounterHistory::COUNTER_ID, self::ID)
            ->with(CounterHistory::CLAIM)
            ->orderBy(CounterHistory::DATE, SearcherInterface::SORT_ORDER_DESC)
            ->orderBy(CounterHistory::ID, SearcherInterface::SORT_ORDER_DESC)
        ;
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, self::ACCOUNT_ID);
    }

    public function passport(): HasOne
    {
        return $this->hasOne(File::class, File::RELATED_ID, 'id')
            ->where(File::TYPE, FileTypeEnum::COUNTER_PASSPORT->value)
        ;
    }
}
