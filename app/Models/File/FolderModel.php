<?php declare(strict_types=1);

namespace App\Models\File;

use App\Models\AbstractModel;
use Carbon\Carbon;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property ?int    $parent_id
 * @property ?string $name
 * @property ?string $uid
 */
class FolderModel extends AbstractModel
{
    public const string TABLE = 'file_folders';

    protected $table = self::TABLE;

    public const string ID        = 'id';
    public const string PARENT_ID = 'parent_id';
    public const string UID       = 'uid';
    public const string NAME      = 'name';

    protected $fillable = [
        self::PARENT_ID,
        self::UID,
        self::NAME,
    ];
}
