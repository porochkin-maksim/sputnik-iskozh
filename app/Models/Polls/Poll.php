<?php

namespace App\Models\Polls;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property string  $title
 * @property ?string $description
 * @property Carbon  $start_at
 * @property Carbon  $end_at
 * @property string  $notify_emails
 */
class Poll extends Model
{
    use HasFactory;

    public const TABLE = 'polls';

    protected $table = self::TABLE;

    public const ID            = 'id';
    public const TITLE         = 'title';
    public const DESCRIPTION   = 'description';
    public const start_at     = 'start_at';
    public const end_at       = 'end_at';
    public const NOTIFY_EMAILS = 'notify_emails';
    public const QUESTIONS     = 'questions';

    protected $fillable = [
        self::TITLE,
        self::DESCRIPTION,
        self::start_at,
        self::end_at,
        self::NOTIFY_EMAILS,
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, Question::POLL_ID);
    }
}
