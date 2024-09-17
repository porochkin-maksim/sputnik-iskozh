<?php

namespace App\Models\Polls;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property int     $poll_id
 * @property int     $type
 * @property string  $text
 * @property string  $options
 * @property Poll    $poll
 */
class Question extends Model
{
    use HasFactory;

    public const TABLE = 'polls_questions';

    protected $table = self::TABLE;

    public const POLL_ID = 'poll_id';
    public const TYPE    = 'type';
    public const TEXT    = 'text';
    public const OPTIONS = 'options';
    public const POLL    = 'poll';

    protected $fillable = [
        self::POLL_ID,
        self::TYPE,
        self::TEXT,
        self::OPTIONS,
    ];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class, self::POLL_ID);
    }
}
