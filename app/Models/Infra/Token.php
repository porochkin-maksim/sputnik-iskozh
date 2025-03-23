<?php

namespace App\Models\Infra;

use App\Models\Interfaces\CastsInterface;
use Illuminate\Database\Eloquent\Model;

/**
 *
 * @property string $id
 * @property string $data
 */
class Token extends Model implements CastsInterface
{
    public const TABLE = 'tokens';

    protected $table = self::TABLE;

    protected $keyType      = 'string';
    public    $incrementing = false;

    public const ID   = 'id';
    public const DATA = 'data';

    protected $guarded = [];
}