<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class AbstractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function getInt(string $key, mixed $default = null): int
    {
        return (int) parent::get($key, $default);
    }

    public function getFloat(string $key, mixed $default = null): ?float
    {
        return is_numeric(parent::get($key, $default)) ? (float) parent::get($key, $default) : null;
    }
}
