<?php

namespace App\Models;

use Core\Objects\Report\Enums\CategoryEnum;
use Core\Objects\Report\Enums\TypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $category
 * @property int $type
 * @property int $year
 * @property ?string $start_at
 * @property ?string $end_at
 * @property ?float $money
 * @property ?int $version
 * @property ?int $parent_id
 */
class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'type',
        'year',
        'start_at',
        'end_at',
        'money',
        'version',
        'parent_id',
    ];

    public function category(): CategoryEnum
    {
        return CategoryEnum::from($this->category);
    }

    public function type(): TypeEnum
    {
        return TypeEnum::from($this->type);
    }
}
