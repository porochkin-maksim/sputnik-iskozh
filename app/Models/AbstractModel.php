<?php declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\CastsInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractModel extends Model implements CastsInterface
{
    public const string TABLE = '';

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $this->table = static::TABLE;
        parent::__construct($attributes);
    }
}