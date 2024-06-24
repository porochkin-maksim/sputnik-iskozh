<?php declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int     $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @property int     $type
 * @property ?string $data
 */
class Option extends Model
{
    public const TABLE = 'options';

    public const ID   = 'id';
    public const TYPE = 'type';
    public const DATA = 'data';

    protected $guarded = [];
}
