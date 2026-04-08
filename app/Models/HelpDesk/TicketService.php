<?php declare(strict_types=1);

namespace App\Models\HelpDesk;

use App\Models\AbstractModel;
use App\Models\Interfaces\CastsInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int            $id
 * @property int            $category_id
 * @property string         $name
 * @property string|null    $code
 * @property int            $sort_order
 * @property bool           $is_active
 * @property Carbon|null    $created_at
 * @property Carbon|null    $updated_at
 *
 * @property TicketCategory $category
 */
class TicketService extends AbstractModel
{
    public const string TABLE = 'ticket_services';

    public const string ID          = 'id';
    public const string CATEGORY_ID = 'category_id';
    public const string NAME        = 'name';
    public const string CODE        = 'code';
    public const string SORT_ORDER  = 'sort_order';
    public const string IS_ACTIVE   = 'is_active';
    public const string CREATED_AT  = 'created_at';
    public const string UPDATED_AT  = 'updated_at';

    public const array PROPERTIES_TO_TITLES = [
        self::CATEGORY_ID => 'Категория',
        self::NAME        => 'Название',
        self::CODE        => 'Код',
        self::SORT_ORDER  => 'Порядок сортировки',
    ];

    public const string RELATION_CATEGORY = 'category';

    protected $guarded = [];

    protected $casts = [
        self::IS_ACTIVE => CastsInterface::CAST_BOOLEAN,
    ];

    // ========== Связи ==========

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, self::CATEGORY_ID);
    }
}
