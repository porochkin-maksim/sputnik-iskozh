<?php declare(strict_types=1);

namespace App\Models\Infra;

use App\Models\AbstractModel;

/**
 *
 * @property string $id
 * @property string $data
 */
class Token extends AbstractModel
{
    public const string TABLE = 'tokens';

    protected $table = self::TABLE;

    protected $keyType      = 'string';
    public    $incrementing = false;

    public const string ID   = 'id';
    public const string DATA = 'data';

    protected $guarded = [];
}