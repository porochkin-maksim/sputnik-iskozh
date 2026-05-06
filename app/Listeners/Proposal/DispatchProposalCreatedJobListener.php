<?php declare(strict_types=1);

namespace App\Listeners\Proposal;

use Core\Domains\Proposal\Events\ProposalCreated;
use App\Jobs\Proposal\ProposalCreatedJob;

class DispatchProposalCreatedJobListener
{
    public function handle(ProposalCreated $event): void
    {
        dispatch(new ProposalCreatedJob(
            $event->proposalFilePath,
            $event->fromName,
            $event->emails,
            $event->tmpFiles,
        ));
    }
}
