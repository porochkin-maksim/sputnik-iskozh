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
class Option extends Model implements CastsInterface
{
    public const TABLE = 'options';

    protected $table = self::TABLE;

    public const ID   = 'id';
    public const TYPE = 'type';
    public const DATA = 'data';

    protected $guarded = [];

    protected $casts = [
        self::DATA => self::CAST_JSON,
    ];
}
