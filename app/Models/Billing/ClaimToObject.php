<?php declare(strict_types=1);

namespace App\Models\Billing;

use App\Models\AbstractModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $type
 * @property int $claim_id
 * @property int $reference_id
 */
class ClaimToObject extends AbstractModel
{
    public const string TABLE = 'claim_to_objects';

    public const string TYPE         = 'type';
    public const string CLAIM_ID     = 'claim_id';
    public const string REFERENCE_ID = 'reference_id';

    public const string RELATION_CLAIM = 'claim';

    protected $table = self::TABLE;

    protected $guarded = [];

    protected $with = [self::RELATION_CLAIM];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class, self::CLAIM_ID);
    }
}
