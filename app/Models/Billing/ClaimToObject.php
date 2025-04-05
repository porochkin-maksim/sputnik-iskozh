<?php declare(strict_types=1);

namespace App\Models\Billing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $type
 * @property int $claim_id
 * @property int $reference_id
 */
class ClaimToObject extends Model
{
    public const TABLE = 'claim_to_objects';

    public const TYPE         = 'type';
    public const CLAIM_ID     = 'claim_id';
    public const REFERENCE_ID = 'reference_id';

    public const CLAIM = 'claim';

    protected $table = self::TABLE;

    protected $guarded = [];

    protected $with = [self::CLAIM];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class, self::CLAIM_ID);
    }
}
