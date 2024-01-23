<?php declare(strict_types=1);

namespace Core\Requests;

use Illuminate\Validation\Rules\Password;

abstract class Rules
{
    public static function Password(): array
    {
        return [
            'required',
            'string',
            'confirmed',
            Password::min(8)
                ->mixedCase()
                ->numbers(),
        ];
    }
}
