<?php

namespace App\Models\File;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
class File extends Model
{
    public const TABLE = 'files';

    protected $table = self::TABLE;

    public const ID         = 'id';
    public const TYPE       = 'type';
    public const RELATED_ID = 'related_id';
    public const PARENT_ID  = 'parent_id';
    public const ORDER      = 'order';
    public const EXT        = 'ext';
    public const NAME       = 'name';
    public const PATH       = 'path';

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
