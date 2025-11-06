<?php declare(strict_types=1);

namespace App\Models\Billing;

use App\Models\Interfaces\CastsInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property int     $invoice_id
 * @property int     $user_id
 * @property int     $payment_id
 * @property int     $provider
 * @property int     $status
 * @property float   $amount
 * @property array   $data
 */
class Acquiring extends Model implements CastsInterface
{
    public const string TABLE = 'acquiring';

    protected $table = self::TABLE;

    public const string ID         = 'id';
    public const string INVOICE_ID = 'invoice_id';
    public const string USER_ID    = 'user_id';
    public const string PAYMENT_ID = 'payment_id';
    public const string PROVIDER   = 'provider';
    public const string STATUS     = 'status';
    public const string AMOUNT     = 'amount';
    public const string DATA       = 'data';

    protected $guarded = [];

    protected $casts = [
        self::AMOUNT     => self::CAST_FLOAT,
        self::INVOICE_ID => self::CAST_INTEGER,
        self::USER_ID    => self::CAST_INTEGER,
        self::PAYMENT_ID => self::CAST_INTEGER,
        self::DATA       => self::CAST_JSON,
    ];
}
