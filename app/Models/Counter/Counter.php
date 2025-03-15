<?php

namespace App\Models\Counter;

use App\Models\Account\Account;
use App\Models\Interfaces\CastsInterface;
use Carbon\Carbon;
use Core\Db\Searcher\SearcherInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 */
class Counter extends Model implements CastsInterface
{
    use SoftDeletes;

    public const TABLE = 'counters';

    protected $table = self::TABLE;

    public const ID           = 'id';
    public const TYPE         = 'type';
    public const ACCOUNT_ID   = 'account_id';
    public const NUMBER       = 'number';
    public const IS_INVOICING = 'is_invoicing';

    public const ACCOUNT = 'account';
    public const HISTORY = 'history';

    protected $guarded = [];
    protected $with    = [self::HISTORY];

    protected $casts = [
        self::IS_INVOICING => self::CAST_BOOLEAN,
    ];

    public function history(): HasMany
    {
        return $this->hasMany(CounterHistory::class, CounterHistory::COUNTER_ID, self::ID)
            ->orderBy(CounterHistory::DATE, SearcherInterface::SORT_ORDER_ASC)
            ->orderBy(CounterHistory::CREATED_AT, SearcherInterface::SORT_ORDER_ASC)
            ->orderBy(CounterHistory::ID, SearcherInterface::SORT_ORDER_ASC)
        ;
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, self::ACCOUNT_ID);
    }
}
