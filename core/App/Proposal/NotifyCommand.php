<?php declare(strict_types=1);

namespace Core\App\Proposal;

use Core\Domains\Proposal\Services\ProposalService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Core\Exceptions\ValidationException;

readonly class NotifyCommand
{
    public function __construct(
        private NotifyValidator $validator,
        private ProposalService $proposalService,
    )
    {
    }

    /**
     * @param UploadedFile[] $files
     *
     * @throws ValidationException
     */
    public function execute(string $fullText, string $name, array $files): void
    {
        $this->validator->validate($fullText, $files);
        $this->proposalService->notify($fullText, $name, $files);
    }
}
