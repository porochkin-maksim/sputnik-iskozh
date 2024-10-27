<?php declare(strict_types=1);

namespace Core\Domains\Proposal\Services;

use Core\Domains\Proposal\Jobs\NewProposalCreatedJob;
use Core\Domains\Proposal\Requests\CreateRequest;
use Core\Notifications\Email\Emails;

class ProposalService
{
    public function notify(CreateRequest $request): void
    {
        dispatch(new NewProposalCreatedJob(
            $request->getFullText(),
            Emails::pressAddresses(),
        ));
    }
}
