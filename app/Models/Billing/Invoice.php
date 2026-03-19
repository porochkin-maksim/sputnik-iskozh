<?php declare(strict_types=1);

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
 * @property ?string $name
 * @property ?string $comment
 */
class Invoice extends Model implements CastsInterface
{
    public const string TABLE = 'invoices';

    protected $table = self::TABLE;

    public const string ID         = 'id';
    public const string PERIOD_ID  = 'period_id';
    public const string ACCOUNT_ID = 'account_id';
    public const string TYPE       = 'type';
    public const string COST       = 'cost';
    public const string PAYED      = 'payed';
    public const string NAME       = 'name';
    public const string COMMENT    = 'comment';

    public const string CLAIMS   = 'claims';
    public const string PAYMENTS = 'payments';
    public const string ACCOUNT  = 'account';
    public const string PERIOD   = 'period';

    protected $guarded = [];

    protected $casts = [
        self::COST  => self::CAST_FLOAT,
        self::PAYED => self::CAST_FLOAT,
    ];

    public const string TITLE_PERIOD_ID  = 'Период';
    public const string TITLE_ACCOUNT_ID = 'Участок';
    public const string TITLE_TYPE       = 'Тип';
    public const string TITLE_PAYED      = 'Оплачено';
    public const string TITLE_COST       = 'Стоимость';
    public const string TITLE_COMMENT    = 'Комментарий';
    public const string TITLE_NAME       = 'Название';

    public const array PROPERTIES_TO_TITLES = [
        self::PERIOD_ID  => self::TITLE_PERIOD_ID,
        self::ACCOUNT_ID => self::TITLE_ACCOUNT_ID,
        self::NAME       => self::TITLE_NAME,
        self::TYPE       => self::TITLE_TYPE,
        self::PAYED      => self::TITLE_PAYED,
        self::COST       => self::TITLE_COST,
        self::COMMENT    => self::TITLE_COMMENT,
    ];

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class, Claim::INVOICE_ID)->with(Claim::SERVICE);
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
