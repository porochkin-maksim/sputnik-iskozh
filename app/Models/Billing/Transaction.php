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
 * @property float   $payed
 */
class Transaction extends Model implements CastsInterface
{
    public const TABLE = 'transactions';

    protected $table = self::TABLE;

    public const ID         = 'id';
    public const INVOICE_ID = 'invoice_id';
    public const SERVICE_ID = 'service_id';
    public const NAME       = 'name';
    public const TARIFF     = 'tariff';
    public const COST       = 'cost';
    public const PAYED      = 'payed';

    public const INVOICE = 'invoice';
    public const SERVICE = 'service';

    protected $guarded = [];

    protected $casts = [
        self::TARIFF => self::CAST_FLOAT,
        self::COST   => self::CAST_FLOAT,
        self::PAYED  => self::CAST_FLOAT,
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
