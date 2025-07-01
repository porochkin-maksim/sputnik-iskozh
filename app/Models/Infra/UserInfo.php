<?php declare(strict_types=1);

namespace App\Models\Infra;

use App\Models\Interfaces\CastsInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int     $id
 * @property int     $user_id
 * @property ?string $ownership_duty_info
 * @property ?Carbon $ownership_date
 */
class UserInfo extends Model implements CastsInterface
{
    public const string TABLE = 'user_info';

    protected $table = self::TABLE;

    public const string ID                  = 'id';
    public const string USER_ID             = 'user_id';
    public const string OWNERSHIP_DATE      = 'ownership_date';
    public const string OWNERSHIP_DUTY_INFO = 'ownership_duty_info';

    protected $guarded    = [];
    public    $timestamps = false;
}
