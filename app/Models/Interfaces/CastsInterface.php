<?php declare(strict_types=1);

namespace App\Models\Interfaces;

interface CastsInterface
{
    public const string CAST_INTEGER  = 'integer';
    public const string CAST_STRING   = 'string';
    public const string CAST_BOOLEAN  = 'boolean';
    public const string CAST_DATE     = 'date';
    public const string CAST_DATETIME = 'datetime';
    public const string CAST_HASHED   = 'hashed';
    public const string CAST_FLOAT    = 'float';
    public const string CAST_JSON     = 'json';
}
