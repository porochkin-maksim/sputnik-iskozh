<?php declare(strict_types=1);

namespace App\Models\Infra;

use App\Models\AbstractModel;
use Carbon\Carbon;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property ?string $data
 */
class Option extends AbstractModel
{
    public const string TABLE = 'options';

    protected $table = self::TABLE;

    public const string ID   = 'id';
    public const string TYPE = 'type';
    public const string DATA = 'data';

    protected $guarded = [];

    protected $casts = [
        self::DATA => self::CAST_JSON,
    ];
}
