<?php declare(strict_types=1);

namespace App\Models\Billing;

use App\Models\AbstractModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property ?int    $id
 * @property ?Carbon $created_at
 *
 * @property int     $payment_id
 * @property ?int    $claim_id
 * @property float   $cost
 */
class Transaction extends AbstractModel
{
    public const string TABLE = 'payment_transactions';

    const null UPDATED_AT = null;

    public const string ID         = 'id';
    public const string PAYMENT_ID = 'payment_id';
    public const string CLAIM_ID   = 'claim_id';
    public const string COST       = 'cost';

    public const string CREATED_AT = 'created_at';

    public const string PAYMENT = 'payment';
    public const string CLAIM   = 'claim';

    protected $casts = [
        self::COST => self::CAST_FLOAT,
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, self::PAYMENT_ID);
    }

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class, self::CLAIM_ID);
    }
}
