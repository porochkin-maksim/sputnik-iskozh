<?php declare(strict_types=1);

namespace App\Models\Interfaces;

interface CastsInterface
{
    const CAST_INTEGER  = 'integer';
    const CAST_STRING   = 'string';
    const CAST_BOOLEAN  = 'boolean';
    const CAST_DATE     = 'date';
    const CAST_DATETIME = 'datetime';
    const CAST_HASHED   = 'hashed';
    const CAST_FLOAT    = 'float';
}
