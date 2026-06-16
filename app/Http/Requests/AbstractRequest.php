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
        return $this->normalizeUploadedFiles($this->rawFiles());
    }

    /**
     * @param string|null $key
     * @param mixed       $default
     *
     * @return UploadedFile|null
     */
    public function file($key = null, $default = null): ?UploadedFile
    {
        $file = $key === null ? null : data_get($this->rawFiles(), $key, $default);

        if ($file instanceof UploadedFile) {
            return $file;
        }

        if ($file instanceof \Illuminate\Http\UploadedFile) {
            return UploadedFileFactory::fromHttpRequest($file);
        }

        return $default;
    }

    public function files(string $key, $default = null): array
    {
        $files = data_get($this->rawFiles(), $key, $default);

        return $this->normalizeUploadedFiles($files);
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

    public function getFloat(string $key, mixed $default = null): float
    {
        return is_numeric($this->input($key, $default)) ? (float) $this->input($key, $default) : 0.0;
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

    /**
     * @return UploadedFile[]
     */
    private function rawFiles(): array
    {
        return parent::allFiles();
    }

    /**
     * @return UploadedFile[]
     */
    private function normalizeUploadedFiles(mixed $files): array
    {
        if ($files instanceof \Illuminate\Http\UploadedFile) {
            return [UploadedFileFactory::fromHttpRequest($files)];
        }

        if ( ! is_array($files) || $files === []) {
            return [];
        }

        $first = reset($files);
        if ($first instanceof \Illuminate\Http\UploadedFile) {
            return UploadedFileFactory::fromHttpRequestCollection($files);
        }

        $result = [];
        foreach ($files as $file) {
            if (is_array($file)) {
                $result = array_merge($result, $this->normalizeUploadedFiles($file));
            }
        }

        return $result;
    }
}
