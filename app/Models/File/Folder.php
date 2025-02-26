<?php

namespace App\Models\File;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property ?int    $parent_id
 * @property ?string $name
 * @property ?string $uid
 */
class Folder extends Model
{
    public const TABLE = 'file_folders';

    protected $table = self::TABLE;

    public const ID        = 'id';
    public const PARENT_ID = 'parent_id';
    public const UID       = 'uid';
    public const NAME      = 'name';

    protected $fillable = [
        self::PARENT_ID,
        self::UID,
        self::NAME,
    ];
}
