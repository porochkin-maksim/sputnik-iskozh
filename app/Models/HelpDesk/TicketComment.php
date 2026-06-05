<?php declare(strict_types=1);

namespace App\Models\HelpDesk;

use App\Models\AbstractModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int         $id
 * @property int         $ticket_id
 * @property int|null    $user_id
 * @property string      $comment
 * @property bool        $is_internal
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Ticket      $ticket
 * @property User|null   $user
 */
class TicketComment extends AbstractModel
{
    public const string TABLE = 'ticket_comments';

    public const string ID          = 'id';
    public const string TICKET_ID   = 'ticket_id';
    public const string USER_ID     = 'user_id';
    public const string COMMENT     = 'comment';
    public const string IS_INTERNAL = 'is_internal';
    public const string CREATED_AT  = 'created_at';
    public const string UPDATED_AT  = 'updated_at';

    public const string RELATION_TICKET = 'ticket';
    public const string RELATION_USER   = 'user';

    public const array PROPERTIES_TO_TITLES = [
        self::TICKET_ID   => 'Заявка',
        self::USER_ID     => 'Пользователь',
        self::COMMENT     => 'Комментарий',
        self::IS_INTERNAL => 'Внутренний комментарий',
    ];

    protected $guarded = [];

    protected $casts = [
        self::TICKET_ID   => self::CAST_INTEGER,
        self::USER_ID     => self::CAST_INTEGER,
        self::IS_INTERNAL => self::CAST_BOOLEAN,
    ];

    // ========== Связи ==========

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, self::TICKET_ID);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::USER_ID)->withTrashed();
    }
}
