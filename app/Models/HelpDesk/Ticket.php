<?php declare(strict_types=1);

namespace App\Models\HelpDesk;

use App\Models\AbstractModel;
use App\Models\Account\Account;
use App\Models\Files\FileModel;
use App\Models\User;
use Carbon\Carbon;
use Core\Domains\Files\FileTypeEnum;
use Core\Domains\HelpDesk\Enums\TicketPriorityEnum;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int                        $id
 * @property int|null                   $user_id
 * @property int|null                   $account_id
 * @property TicketTypeEnum             $type
 * @property int|null                   $category_id
 * @property int|null                   $service_id
 * @property TicketPriorityEnum         $priority
 * @property TicketStatusEnum           $status
 * @property string                     $description
 * @property string|null                $result
 * @property string|null                $contact_name
 * @property string|null                $contact_phone
 * @property string|null                $contact_email
 * @property Carbon|null                $resolved_at
 * @property Carbon|null                $created_at
 * @property Carbon|null                $updated_at
 *
 * @property User|null                  $user
 * @property Account|null               $account
 * @property TicketCategory|null        $category
 * @property TicketService|null         $service
 * @property TicketComment[]|Collection $comments
 * @property FileModel[]|Collection     $files
 * @property FileModel[]|Collection     $resultFiles
 */
class Ticket extends AbstractModel
{
    public const string TABLE = 'tickets';

    // Поля
    public const string ID            = 'id';
    public const string USER_ID       = 'user_id';
    public const string ACCOUNT_ID    = 'account_id';
    public const string TYPE          = 'type';
    public const string CATEGORY_ID   = 'category_id';
    public const string SERVICE_ID    = 'service_id';
    public const string PRIORITY      = 'priority';
    public const string STATUS        = 'status';
    public const string DESCRIPTION   = 'description';
    public const string RESULT        = 'result';
    public const string CONTACT_NAME  = 'contact_name';
    public const string CONTACT_PHONE = 'contact_phone';
    public const string CONTACT_EMAIL = 'contact_email';
    public const string RESOLVED_AT   = 'resolved_at';
    public const string CREATED_AT    = 'created_at';
    public const string UPDATED_AT    = 'updated_at';

    public const array PROPERTIES_TO_TITLES = [
        self::USER_ID       => 'Пользователь',
        self::ACCOUNT_ID    => 'Аккаунт',
        self::TYPE          => 'Тип заявки',
        self::CATEGORY_ID   => 'Категория',
        self::SERVICE_ID    => 'Услуга',
        self::PRIORITY      => 'Приоритет',
        self::STATUS        => 'Статус',
        self::DESCRIPTION   => 'Описание',
        self::RESULT        => 'Результат',
        self::CONTACT_NAME  => 'Контактное лицо',
        self::CONTACT_PHONE => 'Телефон',
        self::CONTACT_EMAIL => 'Email',
        self::RESOLVED_AT   => 'Дата решения',
    ];

    // Связи
    public const string RELATION_USER         = 'user';
    public const string RELATION_ACCOUNT      = 'account';
    public const string RELATION_CATEGORY     = 'category';
    public const string RELATION_SERVICE      = 'service';
    public const string RELATION_COMMENTS     = 'comments';
    public const string RELATION_FILES        = 'files';
    public const string RELATION_RESULT_FILES = 'resultFiles';

    protected $guarded = [];

    protected $casts = [
        self::USER_ID     => self::CAST_INTEGER,
        self::ACCOUNT_ID  => self::CAST_INTEGER,
        self::TYPE        => TicketTypeEnum::class,
        self::PRIORITY    => TicketPriorityEnum::class,
        self::STATUS      => TicketStatusEnum::class,
        self::RESOLVED_AT => self::CAST_DATETIME,
        self::CATEGORY_ID => self::CAST_INTEGER,
        self::SERVICE_ID  => self::CAST_INTEGER,
    ];

    // ========== Связи ==========

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, self::USER_ID)->withTrashed();
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, self::ACCOUNT_ID)->withTrashed();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, self::CATEGORY_ID);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(TicketService::class, self::SERVICE_ID);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class, TicketComment::TICKET_ID)->latest();
    }

    public function files(): HasMany
    {
        return $this->hasMany(FileModel::class, FileModel::RELATED_ID)
            ->where(FileModel::TYPE, FileTypeEnum::TICKET->value)
            ->orderBy(FileModel::ORDER)
        ;
    }

    public function resultFiles(): HasMany
    {
        return $this->hasMany(FileModel::class, FileModel::RELATED_ID)
            ->where(FileModel::TYPE, FileTypeEnum::TICKET_RESULT->value)
            ->orderBy(FileModel::ORDER)
        ;
    }
}
