<?php declare(strict_types=1);

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $type
 * @property int $transaction_id
 * @property int $reference_id
 */
class TransactionToObject extends Model
{
    public const TABLE = 'transaction_to_objects';

    public const TYPE           = 'type';
    public const TRANSACTION_ID = 'transaction_id';
    public const REFERENCE_ID   = 'reference_id';

    public const TRANSACTION = 'transaction';

    protected $table = self::TABLE;

    protected $guarded = [];

    protected $with = [self::TRANSACTION];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, self::TRANSACTION_ID);
    }
}
