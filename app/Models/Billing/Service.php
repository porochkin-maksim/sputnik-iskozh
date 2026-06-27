<?php declare(strict_types=1);

namespace App\Models\Billing;

use App\Models\AbstractModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property string  $name
 * @property int     $type
 * @property int     $period_id
 * @property ?Carbon $period_from
 * @property ?Carbon $period_to
 * @property float   $cost
 */
class Service extends AbstractModel
{
    use HasFactory;
    use SoftDeletes;

    public const string TABLE = 'services';

    protected $table = self::TABLE;

    public const string ID          = 'id';
    public const string TYPE        = 'type';
    public const string PERIOD_ID   = 'period_id';
    public const string PERIOD_FROM = 'period_from';
    public const string PERIOD_TO   = 'period_to';
    public const string NAME        = 'name';
    public const string COST        = 'cost';

    public const string RELATION_PERIOD = 'period';

    protected $guarded = [];

    protected $casts = [
        self::COST        => self::CAST_FLOAT,
        self::PERIOD_FROM => self::CAST_DATETIME,
        self::PERIOD_TO   => self::CAST_DATETIME,
    ];

    public const string TITLE_TYPE        = 'Тип';
    public const string TITLE_PERIOD_ID   = 'Период';
    public const string TITLE_PERIOD_FROM = 'Период действия с';
    public const string TITLE_PERIOD_TO   = 'Период действия по';
    public const string TITLE_NAME        = 'Название';
    public const string TITLE_COST        = 'Стоимость';

    public const array PROPERTIES_TO_TITLES = [
        Service::TYPE        => self::TITLE_TYPE,
        Service::PERIOD_ID   => self::TITLE_PERIOD_ID,
        Service::PERIOD_FROM => self::TITLE_PERIOD_FROM,
        Service::PERIOD_TO   => self::TITLE_PERIOD_TO,
        Service::NAME        => self::TITLE_NAME,
        Service::COST        => self::TITLE_COST,
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class, self::PERIOD_ID);
    }
}
