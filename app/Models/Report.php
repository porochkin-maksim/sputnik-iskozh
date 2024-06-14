<?php

namespace App\Models;

use Carbon\Carbon;
use Core\Domains\File\Enums\TypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property int     $category
 * @property int     $type
 * @property int     $year
 * @property ?string $name
 * @property ?string $start_at
 * @property ?string $end_at
 * @property ?float  $money
 * @property ?int    $version
 * @property ?int    $parent_id
 */
class Report extends Model
{
    use HasFactory;

    const TABLE = 'reports';

    public const NAME      = 'name';
    public const CATEGORY  = 'category';
    public const TYPE      = 'type';
    public const YEAR      = 'year';
    public const START_AT  = 'start_at';
    public const END_AT    = 'end_at';
    public const MONEY     = 'money';
    public const VERSION   = 'version';
    public const PARENT_ID = 'parent_id';

    public const FILES = 'files';

    protected $fillable = [
        self::NAME,
        self::CATEGORY,
        self::TYPE,
        self::YEAR,
        self::START_AT,
        self::END_AT,
        self::MONEY,
        self::VERSION,
        self::PARENT_ID,
    ];

    public function files(): HasMany
    {
        return $this->hasMany(File::class, File::RELATED_ID)->where(File::TYPE, TypeEnum::REPORT->value);
    }
}
