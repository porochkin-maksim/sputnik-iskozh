<?php

namespace App\Models\Billing;

use App\Models\Account\Account;
use App\Models\Interfaces\CastsInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property ?int    $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property ?int    $period_id
 * @property ?int    $account_id
 * @property ?int    $type
 * @property ?float  $cost
 * @property ?float  $payed
 * @property ?string $comment
 */
class Invoice extends Model implements CastsInterface
{
    public const TABLE = 'invoices';

    protected $table = self::TABLE;

    public const ID         = 'id';
    public const PERIOD_ID  = 'period_id';
    public const ACCOUNT_ID = 'account_id';
    public const TYPE       = 'type';
    public const COST       = 'cost';
    public const PAYED      = 'payed';
    public const COMMENT    = 'comment';

    public const TRANSACTIONS = 'transactions';
    public const PAYMENTS     = 'payments';
    public const ACCOUNT      = 'account';
    public const PERIOD       = 'period';

    protected $guarded = [];

    protected $casts = [
        self::COST  => self::CAST_FLOAT,
        self::PAYED => self::CAST_FLOAT,
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, Transaction::INVOICE_ID);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, Payment::INVOICE_ID);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, self::ACCOUNT_ID);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class, self::PERIOD_ID);
    }
}
