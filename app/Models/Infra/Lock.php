<?php declare(strict_types=1);

namespace App\Models\Infra;

use App\Models\Interfaces\CastsInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property ?string $data
 */
class Lock extends Model implements CastsInterface
{
    public const string TABLE = 'locks';

    protected $table = self::TABLE;

    public const string ID        = 'id';
    public const string NAME      = 'name';
    public const string EXPIRE_AT = 'expire_at';

    protected $guarded = [];

    protected $casts = [
        self::EXPIRE_AT => self::CAST_DATETIME,
    ];
}
