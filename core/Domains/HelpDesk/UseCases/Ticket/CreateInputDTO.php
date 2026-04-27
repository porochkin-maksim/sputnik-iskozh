<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\UseCases\Ticket;

use Illuminate\Http\UploadedFile;

readonly class CreateInputDTO
{
    public function __construct(
        public string  $typeCode,
        public string  $categoryCode,
        public string  $serviceCode,
        public string  $description,
        public ?string $contactName,
        public ?string $contactEmail,
        public ?string $contactPhone,
        public ?int    $accountId,
        public ?int    $userId,
        /** @var UploadedFile[] */
        public array   $files,
    )
    {
    }
}
