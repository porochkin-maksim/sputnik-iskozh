<?php declare(strict_types=1);

namespace App\Helpers;

use Core\Domains\Shared\ValueObjects\UploadedFile;
use Illuminate\Http\UploadedFile as LaravelUploadedFile;

class UploadedFileFactory
{
    /**
     * @param LaravelUploadedFile $file
     *
     * @return UploadedFile
     */
    public static function fromHttpRequest(LaravelUploadedFile $file): UploadedFile
    {
        return new UploadedFile(
            name    : $file->getClientOriginalName(),
            path    : $file->getPathname(),
            mimeType: $file->getMimeType(),
            size    : $file->getSize(),
            content : file_get_contents($file->getPathname()),
        );
    }

    /**
     * @param LaravelUploadedFile[] $files
     *
     * @return UploadedFile[]
     */
    public static function fromHttpRequestCollection(array $files): array
    {
        return array_map([self::class, 'fromHttpRequest'], $files);
    }

    /**
     * @param array $files (массив из $_FILES)
     *
     * @return UploadedFile[]
     */
    public static function fromNativeArray(array $files): array
    {
        $result = [];
        foreach ($files as $file) {
            if (isset($file['tmp_name']) && is_uploaded_file($file['tmp_name'])) {
                $result[] = new UploadedFile(
                    name    : $file['name'],
                    path    : $file['tmp_name'],
                    mimeType: $file['type'] ?? mime_content_type($file['tmp_name']),
                    size    : $file['size'],
                    content : file_get_contents($file['tmp_name']),
                );
            }
        }

        return $result;
    }
}
