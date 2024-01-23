<?php

namespace App\Models;

use App\Models\Interfaces\CastsInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model implements CastsInterface
{
    public const TABLE = 'profiles';

    use HasFactory;

    public const USER_ID     = 'user_id';
    public const FIRST_NAME  = 'first_name';
    public const MIDDLE_NAME = 'middle_name';
    public const LAST_NAME   = 'last_name';
    public const EMAIL       = 'email';
    public const TELEGRAM_ID = 'telegram_id';

    protected $fillable = [
        self::USER_ID,
        self::FIRST_NAME,
        self::MIDDLE_NAME,
        self::LAST_NAME,
        self::EMAIL,
        self::TELEGRAM_ID,
    ];

    protected $casts = [
        self::USER_ID     => self::CAST_INTEGER,
        self::FIRST_NAME  => self::CAST_STRING,
        self::MIDDLE_NAME => self::CAST_STRING,
        self::LAST_NAME   => self::CAST_STRING,
        self::EMAIL       => self::CAST_STRING,
        self::TELEGRAM_ID => self::CAST_INTEGER,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
