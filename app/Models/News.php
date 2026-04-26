<?php declare(strict_types=1);

namespace App\Models;

use App\Models\Files\FileModel;
use Carbon\Carbon;
use Core\Domains\Files\FileTypeEnum;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property string  $title
 * @property ?string $description
 * @property ?string $article
 * @property ?bool   $is_lock
 * @property int     $category
 * @property ?Carbon $published_at
 */
class News extends AbstractModel
{
    use SoftDeletes;

    public const string TABLE = 'news';

    public const string ID           = 'id';
    public const string TYPE         = 'type';
    public const string TITLE        = 'title';
    public const string DESCRIPTION  = 'description';
    public const string ARTICLE      = 'article';
    public const string IS_LOCK      = 'is_lock';
    public const string CATEGORY     = 'category';
    public const string PUBLISHED_AT = 'published_at';

    public const string RELATION_FILES = 'files';

    protected $fillable = [
        self::TYPE,
        self::TITLE,
        self::DESCRIPTION,
        self::ARTICLE,
        self::IS_LOCK,
        self::CATEGORY,
        self::PUBLISHED_AT,
    ];

    protected $casts = [
        self::IS_LOCK      => self::CAST_BOOLEAN,
        self::CATEGORY     => self::CAST_INTEGER,
        self::PUBLISHED_AT => self::CAST_DATETIME,
    ];

    public function files(): HasMany
    {
        return $this->hasMany(FileModel::class, FileModel::RELATED_ID)
            ->where(FileModel::TYPE, FileTypeEnum::NEWS->value)
            ->orderBy(FileModel::ORDER);
    }
}
