<?php declare(strict_types=1);

namespace App\Models\Billing;

use App\Models\AbstractModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property int     $invoice_id
 * @property int     $service_id
 * @property int     $original_service_id
 * @property ?int    $original_claim_id
 * @property string  $name
 * @property float   $tariff
 * @property float   $cost
 * @property float   $paid
 * @property ?float  $quantity
 */
class Claim extends AbstractModel
{
    use HasFactory;

    public const string TABLE = 'claims';

    protected $table = self::TABLE;

    public const string ID                 = 'id';
    public const string INVOICE_ID         = 'invoice_id';
    public const string SERVICE_ID         = 'service_id';
    public const string ORIGINAL_SERVICE_ID = 'original_service_id';
    public const string ORIGINAL_CLAIM_ID   = 'original_claim_id';
    public const string NAME               = 'name';
    public const string TARIFF             = 'tariff';
    public const string COST               = 'cost';
    public const string PAID               = 'paid';
    public const string QUANTITY           = 'quantity';

    public const string RELATION_INVOICE          = 'invoice';
    public const string RELATION_SERVICE          = 'service';
    public const string RELATION_ORIGINAL_SERVICE = 'originalService';

    protected $guarded = [];

    protected $casts = [
        self::TARIFF              => self::CAST_FLOAT,
        self::COST                => self::CAST_FLOAT,
        self::PAID                => self::CAST_FLOAT,
        self::QUANTITY            => self::CAST_FLOAT,
        self::ORIGINAL_SERVICE_ID => 'integer',
        self::ORIGINAL_CLAIM_ID   => 'integer',
    ];

    public const string TITLE_NAME                 = 'Название';
    public const string TITLE_INVOICE_ID           = 'Счёт';
    public const string TITLE_SERVICE_ID           = 'Услуга';
    public const string TITLE_ORIGINAL_SERVICE_ID  = 'Исходная услуга';
    public const string TITLE_ORIGINAL_CLAIM_ID    = 'Исходная услуга (claim)';
    public const string TITLE_TARIFF               = 'Тариф';
    public const string TITLE_COST                 = 'Стоимость';
    public const string TITLE_PAID                 = 'Оплачено';
    public const string TITLE_QUANTITY             = 'Количество';

    public const array PROPERTIES_TO_TITLES = [
        self::INVOICE_ID           => self::TITLE_INVOICE_ID,
        self::SERVICE_ID           => self::TITLE_SERVICE_ID,
        self::ORIGINAL_SERVICE_ID  => self::TITLE_ORIGINAL_SERVICE_ID,
        self::ORIGINAL_CLAIM_ID    => self::TITLE_ORIGINAL_CLAIM_ID,
        self::TARIFF               => self::TITLE_TARIFF,
        self::COST                 => self::TITLE_COST,
        self::PAID                 => self::TITLE_PAID,
        self::QUANTITY             => self::TITLE_QUANTITY,
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, self::INVOICE_ID);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, Transaction::CLAIM_ID);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, self::SERVICE_ID);
    }

    public function originalService(): BelongsTo
    {
        return $this->belongsTo(Service::class, self::ORIGINAL_SERVICE_ID);
    }
}
