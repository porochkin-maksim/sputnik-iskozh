<?php declare(strict_types=1);

namespace App\Models\Infra;

use App\Models\AbstractModel;

/**
 *
 * @property string $id
 * @property int    $type
 * @property int    $reference_id
 */
class Uid extends AbstractModel
{
    public const string TABLE = 'uids';

    protected $table = self::TABLE;

    protected $keyType      = 'string';
    public    $incrementing = false;
    public    $timestamps   = false;

    public const string ID           = 'id';
    public const string TYPE         = 'type';
    public const string REFERENCE_ID = 'reference_id';

    protected $guarded = [];
}