<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int     $id
 * @property int     $type
 * @property int     $related_id
 * @property ?string $ext
 * @property ?string $name
 * @property ?string $path
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class File extends Model
{
    use HasFactory;

    public const TABLE = 'files';

    public const ID         = 'id';
    public const TYPE       = 'type';
    public const RELATED_ID = 'related_id';
    public const EXT        = 'ext';
    public const NAME       = 'name';
    public const PATH       = 'path';

    protected $fillable = [
        self::TYPE,
        self::RELATED_ID,
        self::EXT,
        self::NAME,
        self::PATH,
    ];
}
