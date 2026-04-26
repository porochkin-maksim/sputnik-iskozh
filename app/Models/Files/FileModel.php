<?php declare(strict_types=1);

namespace App\Models\Files;

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
class FileModel extends AbstractModel
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

    protected $guarded = [];

    public const string TITLE_NAME = 'Название';

    public const array PROPERTIES_TO_TITLES = [
        self::NAME => self::TITLE_NAME,
    ];
}
