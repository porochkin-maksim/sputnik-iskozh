<?php

namespace App\Models\Billing;

use App\Models\Interfaces\CastsInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property int     $invoice_id
 * @property int     $service_id
 * @property string  $name
 * @property float   $tariff
 * @property float   $cost
 * @property float   $paid
 */
class Claim extends Model implements CastsInterface
{
    public const TABLE = 'claims';

    protected $table = self::TABLE;

    public const string ID         = 'id';
    public const string INVOICE_ID = 'invoice_id';
    public const string SERVICE_ID = 'service_id';
    public const string NAME       = 'name';
    public const string TARIFF     = 'tariff';
    public const string COST       = 'cost';
    public const string PAID       = 'paid';

    public const string INVOICE = 'invoice';
    public const string SERVICE = 'service';

    protected $guarded = [];

    protected $casts = [
        self::TARIFF => self::CAST_FLOAT,
        self::COST   => self::CAST_FLOAT,
        self::PAID   => self::CAST_FLOAT,
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, self::INVOICE_ID);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, self::SERVICE_ID);
    }
}
