<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Базовый абстрактный класс для всех моделей.
 *
 * @mixin Builder
 *
 * @property int         $id
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @method static Builder|static query()
 * @method static static|null find($id, array $columns = ['*'])
 * @method static static findOrFail($id, array $columns = ['*'])
 * @method static static|null first()
 * @method static Builder where(string $column, $operator = null, $value = null, $boolean = 'and')
 *
 * @method null|static find($id)   // @deprecated используйте find() напрямую
 */
abstract class AbstractModel extends Model
{
    public const string CAST_INTEGER  = 'integer';
    public const string CAST_STRING   = 'string';
    public const string CAST_BOOLEAN  = 'boolean';
    public const string CAST_DATE     = 'date';
    public const string CAST_DATETIME = 'datetime';
    public const string CAST_HASHED   = 'hashed';
    public const string CAST_FLOAT    = 'float';
    public const string CAST_JSON     = 'json';

    public const string TABLE = '';

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $this->table = static::TABLE;
        parent::__construct($attributes);
    }
}
