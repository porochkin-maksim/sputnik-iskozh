<?php declare(strict_types=1);

namespace Core\Domains\Proposal\Events;

use App\Services\Files\Collections\TmpFiles;

readonly class ProposalCreated
{
    /**
     * @param string[] $emails
     */
    public function __construct(
        public string   $proposalFilePath,
        public string   $fromName,
        public array    $emails,
        public TmpFiles $tmpFiles,
    )
    {
    }
}
