<?php declare(strict_types=1);

namespace Core\Domains\News;

use Core\Domains\Files\FileService as BaseFileService;
use Core\Domains\Files\FileTypeEnum;

readonly class FileService extends BaseFileService
{
    protected function getBaseDir(): string
    {
        return 'news';
    }

    protected function getBaseType(): ?FileTypeEnum
    {
        return FileTypeEnum::NEWS;
    }
}
