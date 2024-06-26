<?php

namespace App\Models\Access;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 */
class Role extends Model
{
    use HasFactory;

    public const TABLE = 'roles';

    public const ID = 'id';

    public $timestamps = false;

    protected $guarded = [];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            RoleToUser::TABLE,
            RoleToUser::ROLE,
            RoleToUser::USER,
        );
    }
}
