<?php declare(strict_types=1);

namespace Core\App\HelpDesk\Ticket;

use Core\Domains\HelpDesk\Models\TicketEntity;
use Core\Domains\HelpDesk\Services\TicketService;
use Core\Exceptions\ValidationException;

readonly class SaveCommand
{
    public function __construct(
        private TicketService $ticketService,
        private UpdateCommand $updateCommand,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(
        int     $id,
        string  $description,
        ?string $result,
        int     $type,
        int     $categoryId,
        int     $serviceId,
        int     $priority,
        int     $status,
        ?string $contactName,
        ?string $contactPhone,
        ?string $contactEmail,
        int     $userId,
        int     $accountId,
        array   $files,
        array   $resultFiles,
    ): ?TicketEntity
    {
        if ($this->ticketService->getById($id) === null) {
            return null;
        }

        return $this->updateCommand->execute(new UpdateInput(
            id          : $id,
            description : $description,
            result      : $result,
            type        : $type,
            categoryId  : $categoryId,
            serviceId   : $serviceId,
            priority    : $priority,
            status      : $status,
            contactName : $contactName,
            contactPhone: $contactPhone,
            contactEmail: $contactEmail,
            userId      : $userId,
            accountId   : $accountId,
            files       : $files,
            resultFiles : $resultFiles,
        ));
    }
}
