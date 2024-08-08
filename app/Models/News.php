<?php

namespace App\Models;

use App\Models\File\File;
use App\Models\Interfaces\CastsInterface;
use Carbon\Carbon;
use Core\Domains\File\Enums\TypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property string  $title
 * @property ?string $article
 * @property ?bool   $is_lock
 * @property int     $category
 * @property ?Carbon $published_at
 */
class News extends Model implements CastsInterface
{
    use HasFactory;

    const TABLE = 'news';

    public const TYPE         = 'type';
    public const TITLE        = 'title';
    public const ARTICLE      = 'article';
    public const IS_LOCK      = 'is_lock';
    public const CATEGORY     = 'category';
    public const PUBLISHED_AT = 'published_at';

    public const FILES = 'files';

    protected $fillable = [
        self::TYPE,
        self::TITLE,
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
        return $this->hasMany(File::class, File::RELATED_ID)
            ->where(File::TYPE, TypeEnum::NEWS->value)
            ->orderBy(FILE::ORDER);
    }
}
