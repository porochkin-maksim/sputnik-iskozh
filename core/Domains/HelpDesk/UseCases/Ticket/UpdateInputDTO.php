<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\UseCases\Ticket;

use Illuminate\Http\UploadedFile;

readonly class UpdateInputDTO
{
    public function __construct(
        public ?int    $id,
        public string  $description,
        public ?string $result,
        public ?int    $type,
        public ?int    $categoryId,
        public ?int    $serviceId,
        public ?int    $priority,
        public ?int    $status,
        public ?string $contactName,
        public ?string $contactPhone,
        public ?string $contactEmail,
        public ?int    $userId,
        public ?int    $accountId,
        /** @var UploadedFile[] */
        public array   $files,
        public array   $resultFiles,
    )
    {
    }
}
