<?php declare(strict_types=1);

namespace App\Models\Infra;

use App\Models\AbstractModel;
use Carbon\Carbon;

/**
 * @property int     $id
 * @property int     $user_id
 * @property ?string $membership_duty_info
 * @property ?Carbon $membership_date
 */
class UserInfo extends AbstractModel
{
    public const string TABLE = 'user_info';

    protected $table = self::TABLE;

    public const string ID                   = 'id';
    public const string USER_ID              = 'user_id';
    public const string MEMBERSHIP_DATE      = 'membership_date';
    public const string MEMBERSHIP_DUTY_INFO = 'membership_duty_info';

    protected $guarded    = [];
    public    $timestamps = false;
}
