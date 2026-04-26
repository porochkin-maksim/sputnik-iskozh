<?php declare(strict_types=1);

namespace App\Models\HelpDesk;

use App\Models\AbstractModel;
use Carbon\Carbon;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int            $id
 * @property TicketTypeEnum $type
 * @property string         $name
 * @property string         $code
 * @property int            $sort_order
 * @property bool           $is_active
 * @property Carbon|null    $created_at
 * @property Carbon|null    $updated_at
 *
 * @property TicketService[]|Collection $services
 */
class TicketCategory extends AbstractModel
{
    public const string TABLE = 'ticket_categories';

    public const string ID         = 'id';
    public const string TYPE       = 'type';
    public const string NAME       = 'name';
    public const string CODE       = 'code';
    public const string SORT_ORDER = 'sort_order';
    public const string IS_ACTIVE  = 'is_active';
    public const string CREATED_AT = 'created_at';
    public const string UPDATED_AT = 'updated_at';

    public const array PROPERTIES_TO_TITLES = [
        self::TYPE       => 'Тип заявки',
        self::NAME       => 'Название',
        self::CODE       => 'Код',
        self::SORT_ORDER => 'Порядок сортировки',
        self::IS_ACTIVE  => 'Активна',
    ];

    public const string RELATION_SERVICES = 'services';

    protected $guarded = [];

    protected $casts = [
        self::TYPE       => TicketTypeEnum::class,
        self::IS_ACTIVE  => self::CAST_BOOLEAN,
        self::SORT_ORDER => self::CAST_INTEGER,
    ];

    // ========== Связи ==========

    public function services(): HasMany
    {
        return $this
            ->hasMany(TicketService::class, TicketService::CATEGORY_ID)
            ->orderBy(TicketService::SORT_ORDER)
        ;
    }
}
