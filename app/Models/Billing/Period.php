<?php declare(strict_types=1);

namespace App\Models\Billing;

use App\Models\AbstractModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property string  $name
 * @property Carbon  $start_at
 * @property Carbon  $end_at
 * @property boolean $is_closed
 */
class Period extends AbstractModel
{
    use SoftDeletes;

    public const string TABLE = 'periods';

    protected $table = self::TABLE;

    public const string ID        = 'id';
    public const string NAME      = 'name';
    public const string START_AT  = 'start_at';
    public const string END_AT    = 'end_at';
    public const string IS_CLOSED = 'is_closed';

    protected $guarded = [];

    protected $casts = [
        self::START_AT  => self::CAST_DATETIME,
        self::END_AT    => self::CAST_DATETIME,
        self::IS_CLOSED => 'boolean',
    ];

    public const string TITLE_NAME     = 'Название';
    public const string TITLE_START_AT = 'Начало';
    public const string TITLE_END_AT   = 'Окончание';

    public const array PROPERTIES_TO_TITLES = [
        self::NAME     => self::TITLE_NAME,
        self::START_AT => self::TITLE_START_AT,
        self::END_AT   => self::TITLE_END_AT,
    ];
}
