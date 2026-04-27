<?php declare(strict_types=1);

namespace App\Models\Billing;

use App\Models\AbstractModel;
use App\Models\Account\Account;
use Carbon\Carbon;
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
 * @property ?float  $paid
 * @property ?float  $advance
 * @property ?float  $debt
 * @property ?string $name
 * @property ?string $comment
 */
class Invoice extends AbstractModel
{
    public const string TABLE = 'invoices';

    protected $table = self::TABLE;

    public const string ID         = 'id';
    public const string PERIOD_ID  = 'period_id';
    public const string ACCOUNT_ID = 'account_id';
    public const string TYPE       = 'type';
    public const string COST       = 'cost';
    public const string PAID       = 'paid';
    public const string ADVANCE    = 'advance';
    public const string DEBT       = 'debt';
    public const string NAME       = 'name';
    public const string COMMENT    = 'comment';

    public const string RELATION_CLAIMS   = 'claims';
    public const string RELATION_PAYMENTS = 'payments';
    public const string RELATION_ACCOUNT  = 'account';
    public const string RELATION_PERIOD   = 'period';

    protected $guarded = [];

    protected $casts = [
        self::COST    => self::CAST_FLOAT,
        self::PAID    => self::CAST_FLOAT,
        self::ADVANCE => self::CAST_FLOAT,
        self::DEBT    => self::CAST_FLOAT,
    ];

    public const string TITLE_PERIOD_ID  = 'Период';
    public const string TITLE_ACCOUNT_ID = 'Участок';
    public const string TITLE_TYPE       = 'Тип';
    public const string TITLE_PAID       = 'Оплачено';
    public const string TITLE_COST       = 'Стоимость';
    public const string TITLE_ADVANCE    = 'Аванс';
    public const string TITLE_DEBT       = 'Долг';
    public const string TITLE_COMMENT    = 'Комментарий';
    public const string TITLE_NAME       = 'Название';

    public const array PROPERTIES_TO_TITLES = [
        self::PERIOD_ID  => self::TITLE_PERIOD_ID,
        self::ACCOUNT_ID => self::TITLE_ACCOUNT_ID,
        self::NAME       => self::TITLE_NAME,
        self::TYPE       => self::TITLE_TYPE,
        self::PAID       => self::TITLE_PAID,
        self::COST       => self::TITLE_COST,
        self::ADVANCE    => self::TITLE_ADVANCE,
        self::DEBT       => self::TITLE_DEBT,
        self::COMMENT    => self::TITLE_COMMENT,
    ];

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class, Claim::INVOICE_ID)->with(Claim::RELATION_SERVICE);
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
