<?php declare(strict_types=1);

namespace App\Models\Billing;

use App\Models\AbstractModel;
use Carbon\Carbon;
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
 * @property float   $cost
 * @property bool    $active
 */
class Service extends AbstractModel
{
    use SoftDeletes;

    public const string TABLE = 'services';

    protected $table = self::TABLE;

    public const string ID        = 'id';
    public const string TYPE      = 'type';
    public const string PERIOD_ID = 'period_id';
    public const string NAME      = 'name';
    public const string COST      = 'cost';
    public const string ACTIVE    = 'active';

    public const string RELATION_PERIOD = 'period';

    protected $guarded = [];

    protected $casts = [
        self::COST   => self::CAST_FLOAT,
        self::ACTIVE => self::CAST_BOOLEAN,
    ];

    public const string TITLE_TYPE      = 'Тип';
    public const string TITLE_PERIOD_ID = 'Период';
    public const string TITLE_NAME      = 'Название';
    public const string TITLE_COST      = 'Стоимость';
    public const string TITLE_ACTIVE    = 'Активен';

    public const array PROPERTIES_TO_TITLES = [
        Service::TYPE      => self::TITLE_TYPE,
        Service::PERIOD_ID => self::TITLE_PERIOD_ID,
        Service::NAME      => self::TITLE_NAME,
        Service::COST      => self::TITLE_COST,
        Service::ACTIVE    => self::TITLE_ACTIVE,
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class, self::PERIOD_ID);
    }
}
