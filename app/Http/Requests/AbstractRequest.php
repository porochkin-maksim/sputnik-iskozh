<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

/**
 * @method UploadedFile[] allFiles()
 */
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

    public function getBool(string $key): bool
    {
        if (parent::has($key)) {
            if ($this->get($key) === true || $this->get($key) === 'true') {
                return true;
            }
        }

        return false;
    }

    public function getIntOrNull(string $key): ?int
    {
        if (parent::has($key)) {
            if ($this->get($key) === null || $this->get($key) === 'null') {
                return null;
            }

            return $this->getInt($key);
        }

        return null;
    }

    public function getStringOrNull(string $key): ?string
    {
        if (parent::has($key)) {
            if ($this->get($key) === null || $this->get($key) === 'null') {
                return null;
            }

            return $this->get($key);
        }

        return null;
    }

    public function getFloat(string $key, mixed $default = null): ?float
    {
        return is_numeric(parent::get($key, $default)) ? (float) parent::get($key, $default) : null;
    }

    public function getDateOrNull(string $key): ?Carbon
    {
        try {
            return $this->get($key) ? Carbon::parse($this->get($key)) : null;
        }
        catch (\Exception) {
            return null;
        }
    }
}
