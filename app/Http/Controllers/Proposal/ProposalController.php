<?php declare(strict_types=1);

namespace App\Http\Controllers\Proposal;

use App\Http\Controllers\Controller;
use Core\Domains\Proposal\ProposalLocator;
use Core\Domains\Proposal\Requests\CreateRequest;
use Core\Domains\Proposal\Services\ProposalService;

class ProposalController extends Controller
{
    private ProposalService $proposalService;

    public function __construct()
    {
        $this->proposalService = ProposalLocator::proposalService();
    }

    public function create(CreateRequest $request): void
    {
        $this->proposalService->notify($request);
    }
}
