<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

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
        return (int) $this->get($key, $default);
    }

    public function getBool(string $key): bool
    {
        if ($this->has($key)) {
            if (in_array($this->get($key), [true, 'true'], true)) {
                return true;
            }
        }

        return false;
    }

    public function getIntOrNull(string $key): ?int
    {
        if ($this->has($key)) {
            if (in_array($this->get($key), [null, 'null'], true)) {
                return null;
            }

            return $this->getInt($key);
        }

        return null;
    }

    public function getString(string $key): string
    {
        if ($this->has($key)) {
            if (in_array(Str::lower($this->get($key)), [null, 'null', 'nan'], true)) {
                return '';
            }

            return (string) $this->get($key);
        }

        return '';
    }

    public function getStringOrNull(string $key): ?string
    {
        if ($this->has($key)) {
            if (in_array(Str::lower($this->get($key)), [null, 'null', 'nan'], true)) {
                return null;
            }

            return $this->get($key);
        }

        return null;
    }

    public function getFloat(string $key, mixed $default = null): ?float
    {
        return is_numeric($this->get($key, $default)) ? (float) $this->get($key, $default) : null;
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

    public function getArray(string $key, array $default = [], string $callback = ''): array
    {
        if ( ! is_array($this->get($key))) {
            return $default;
        }

        if ($callback) {
            return array_map($callback, $this->get($key));
        }

        return $this->get($key, $default);
    }
}
