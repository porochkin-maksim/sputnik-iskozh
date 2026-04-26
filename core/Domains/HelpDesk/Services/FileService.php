<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Services;

use Core\Domains\Files\FileService as BaseFileService;
use Core\Domains\Files\FileTypeEnum;

readonly class FileService extends BaseFileService
{
    protected function getBaseDir(): string
    {
        return 'help-desk/tickets';
    }

    protected function getBaseType(): ?FileTypeEnum
    {
        return FileTypeEnum::TICKET;
    }

    protected function isDefaultPublic(): ?bool
    {
        return false;
    }
}
