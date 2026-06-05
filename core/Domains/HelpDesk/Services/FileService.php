<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Services;

use Core\Domains\Files\FileSearcher;
use Core\Domains\Files\FileTypeEnum;
use Core\Domains\Files\FileService as BaseFileService;
use Core\Domains\Files\FileServiceConfig;
use Core\Domains\Shared\ValueObjects\UploadedFile;

readonly class FileService extends BaseFileService
{
    protected function config(): FileServiceConfig
    {
        return new FileServiceConfig(
            baseDir      : 'help-desk/tickets',
            baseType     : FileTypeEnum::TICKET,
            defaultPublic: false,
        );
    }

    /**
     * @param UploadedFile[] $files
     */
    public function storeTicketFiles(array $files, int $ticketId): void
    {
        $this->storeAndSave($files, $ticketId);
    }

    /**
     * @param UploadedFile[] $files
     */
    public function storeTicketResultFiles(array $files, int $ticketId): void
    {
        $this->storeAndSave($files, $ticketId, FileTypeEnum::TICKET_RESULT);
    }

    public function deleteTicketFiles(int $ticketId): void
    {
        $files = $this->search($this->makeTicketFilesSearcher($ticketId))->getItems();

        foreach ($files as $file) {
            $this->deleteById($file->getId());
        }
    }

    private function makeTicketFilesSearcher(int $ticketId): FileSearcher
    {
        return (new FileSearcher())
            ->setRelatedId($ticketId)
            ->setType(FileTypeEnum::TICKET)
        ;
    }
}
