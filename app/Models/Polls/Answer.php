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
 * @property int     $question_id
 * @property string  $value
 * @property Poll    $poll
 */
class Answer extends Model
{
    use HasFactory;

    public const TABLE = 'polls_answers';

    protected $table = self::TABLE;

    public const QUESTION_ID = 'question_id';
    public const VALUE       = 'value';
    public const QUESTION    = 'question';

    protected $fillable = [
        self::QUESTION_ID,
        self::VALUE,
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, self::Q);
    }
}
