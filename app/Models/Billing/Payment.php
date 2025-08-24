<?php declare(strict_types=1);

namespace App\Models\Billing;

use App\Models\AbstractModel;
use App\Models\Account\Account;
use App\Models\File\File;
use Carbon\Carbon;
use Core\Domains\File\Enums\FileTypeEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property ?int     $id
 * @property ?Carbon  $created_at
 * @property ?Carbon  $updated_at
 *
 * @property ?int     $invoice_id
 * @property ?int     $account_id
 * @property ?float   $cost
 * @property ?boolean $moderated
 * @property ?boolean $verified
 * @property ?string  $comment
 * @property ?string  $name
 * @property ?array   $data
 * @property ?string  $account_number
 * @property ?Carbon  $payed_at
 */
class Payment extends AbstractModel
{
    public const string TABLE = 'payments';

    public const string ID         = 'id';
    public const string INVOICE_ID = 'invoice_id';
    public const string ACCOUNT_ID = 'account_id';
    public const string COST       = 'cost';
    public const string MODERATED  = 'moderated';
    public const string VERIFIED   = 'verified';
    public const string COMMENT    = 'comment';
    public const string NAME       = 'name';
    public const string DATA       = 'data';
    public const string PAYED_AT   = 'payed_at';

    public const string ACCOUNT = 'account';
    public const string INVOICE = 'invoice';
    public const string FILES   = 'files';

    protected $casts = [
        self::COST      => self::CAST_FLOAT,
        self::MODERATED => self::CAST_BOOLEAN,
        self::VERIFIED  => self::CAST_BOOLEAN,
        self::DATA      => self::CAST_JSON,
        self::PAYED_AT  => self::CAST_DATE,
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, self::INVOICE_ID);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, self::ACCOUNT_ID);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class, File::RELATED_ID)
            ->where(File::TYPE, FileTypeEnum::PAYMENT->value)
            ->orderBy(FILE::ORDER)
        ;
    }
}
