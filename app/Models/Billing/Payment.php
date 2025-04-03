<?php

namespace App\Models\Billing;

use App\Models\Account\Account;
use App\Models\File\File;
use App\Models\Interfaces\CastsInterface;
use Carbon\Carbon;
use Core\Domains\File\Enums\FileTypeEnum;
use Illuminate\Database\Eloquent\Model;
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
 */
class Payment extends Model implements CastsInterface
{
    public const TABLE = 'payments';

    protected $table = self::TABLE;

    public const ID         = 'id';
    public const INVOICE_ID = 'invoice_id';
    public const ACCOUNT_ID = 'account_id';
    public const COST       = 'cost';
    public const MODERATED  = 'moderated';
    public const VERIFIED   = 'verified';
    public const COMMENT    = 'comment';
    public const NAME       = 'name';
    public const DATA       = 'data';

    public const ACCOUNT = 'account';
    public const INVOICE = 'invoice';
    public const FILES   = 'files';

    protected $guarded = [];

    protected $casts = [
        self::COST      => self::CAST_FLOAT,
        self::MODERATED => self::CAST_BOOLEAN,
        self::VERIFIED  => self::CAST_BOOLEAN,
        self::DATA      => self::CAST_JSON,
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
