<?php declare(strict_types=1);

namespace App\Models\Infra;

use App\Models\AbstractModel;
use Carbon\Carbon;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property string  $type
 * @property int     $reference_id
 * @property array   $data
 */
class ExData extends AbstractModel
{
    public const string TABLE = 'ex_data';

    protected $table = self::TABLE;

    public const string ID           = 'id';
    public const string TYPE         = 'type';
    public const string REFERENCE_ID = 'reference_id';
    public const string DATA         = 'data';

    protected $guarded = [];

    protected $casts = [
        self::DATA => self::CAST_JSON,
    ];
}
