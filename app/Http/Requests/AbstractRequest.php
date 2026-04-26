<?php declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\UploadedFileFactory;
use Carbon\Carbon;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

abstract class AbstractRequest extends FormRequest
{
    public static function make()
    {
        $request = request();

        return new static(
            $request->query->all(),
            $request->request->all(),
            $request->attributes->all(),
            $request->cookies->all(),
            $request->files->all(),
            $request->server->all(),
            $request->content,
        );
    }

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return UploadedFile[]
     */
    public function allFiles(): array
    {
        return UploadedFileFactory::fromHttpRequestCollection(parent::allFiles());
    }

    /**
     * @param string|null $key
     * @param mixed       $default
     *
     * @return UploadedFile|null
     */
    public function file($key = null, $default = null)
    {
        $file = parent::file($key, $default);

        if ($file instanceof UploadedFile) {
            return $file;
        }

        return $file ? UploadedFileFactory::fromHttpRequest($file) : $default;
    }

    public function files(string $key, $default = null)
    {
        $files = parent::file($key, $default);
        $files = is_array($files) ? $files : [$files];

        return $files ? UploadedFileFactory::fromHttpRequestCollection($files) : $default;
    }

    public function getInt(string $key, mixed $default = null): int
    {
        return (int) $this->input($key, $default);
    }

    public function getBool(string $key, mixed $default = null): bool
    {
        if ($this->has($key) && in_array($this->input($key), [true, 'true'], true)) {
            return true;
        }

        return (bool) $default;
    }

    public function getIntOrNull(string $key, mixed $default = null): ?int
    {
        if ($this->has($key)) {
            if (in_array($this->input($key), [null, 'null'], true)) {
                return null;
            }

            return $this->getInt($key);
        }

        return is_numeric($default) ? (int) $default : null;
    }

    public function getString(string $key, mixed $default = null): string
    {
        if ($this->has($key)) {
            if (in_array(Str::lower($this->input($key)), [null, 'null', 'nan'], true)) {
                return '';
            }

            return (string) $this->input($key);
        }

        return trim((string) $default);
    }

    public function getStringOrNull(string $key, mixed $default = null): ?string
    {
        if ($this->has($key)) {
            if (in_array(Str::lower($this->input($key)), [null, 'null', 'nan'], true)) {
                return null;
            }

            return $this->input($key);
        }

        return trim((string) $default) ? : null;
    }

    public function getFloat(string $key, mixed $default = null): ?float
    {
        return is_numeric($this->input($key, $default)) ? (float) $this->input($key, $default) : null;
    }

    public function getDateOrNull(string $key, ?string $fromFormat = null): ?Carbon
    {
        try {
            if ($fromFormat) {
                return $this->input($key) ? Carbon::createFromFormat($fromFormat, $this->input($key)) : null;
            }

            return $this->input($key) ? Carbon::parse($this->input($key)) : null;
        }
        catch (\Exception) {
            return null;
        }
    }

    public function getArray(string $key, array $default = [], string $callback = ''): array
    {
        if ( ! is_array($this->input($key))) {
            return $default;
        }

        if ($callback) {
            return array_map($callback, $this->input($key));
        }

        return $this->input($key, $default);
    }
}
