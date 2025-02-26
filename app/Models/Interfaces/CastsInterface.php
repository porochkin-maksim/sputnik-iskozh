<?php declare(strict_types=1);

namespace App\Models\Interfaces;

interface CastsInterface
{
    public const CAST_INTEGER  = 'integer';
    public const CAST_STRING   = 'string';
    public const CAST_BOOLEAN  = 'boolean';
    public const CAST_DATE     = 'date';
    public const CAST_DATETIME = 'datetime';
    public const CAST_HASHED   = 'hashed';
    public const CAST_FLOAT    = 'float';
    public const CAST_JSON     = 'json';
}
