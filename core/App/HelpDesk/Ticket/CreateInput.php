<?php declare(strict_types=1);

namespace Core\App\HelpDesk\Ticket;

use Core\Domains\Shared\ValueObjects\UploadedFile;

readonly class CreateInput
{
    public function __construct(
        public string $typeCode,
        public string $categoryCode,
        public string $serviceCode,
        public string $description,
        public ?string $contactName,
        public ?string $contactEmail,
        public ?string $contactPhone,
        public ?int $accountId,
        public ?int $userId,
        /** @var UploadedFile[] */
        public array $files,
    )
    {
    }
}
