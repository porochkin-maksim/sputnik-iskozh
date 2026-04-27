<?php declare(strict_types=1);

namespace App\Models\File;

use App\Models\AbstractModel;
use Carbon\Carbon;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property int     $type
 * @property int     $related_id
 * @property ?int    $parent_id
 * @property int     $order
 * @property ?string $ext
 * @property ?string $name
 * @property ?string $path
 */
class File extends AbstractModel
{
    public const string TABLE = 'files';

    protected $table = self::TABLE;

    public const string ID         = 'id';
    public const string TYPE       = 'type';
    public const string RELATED_ID = 'related_id';
    public const string PARENT_ID  = 'parent_id';
    public const string ORDER      = 'order';
    public const string EXT        = 'ext';
    public const string NAME       = 'name';
    public const string PATH       = 'path';

    protected $fillable = [
        self::TYPE,
        self::RELATED_ID,
        self::PARENT_ID,
        self::ORDER,
        self::EXT,
        self::NAME,
        self::PATH,
    ];
}
