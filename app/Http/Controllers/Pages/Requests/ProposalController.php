<?php declare(strict_types=1);

namespace App\Http\Controllers\Pages\Requests;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Requests\ProposalCreateRequest;
use Core\Domains\Proposal\ProposalLocator;
use Core\Domains\Proposal\Services\ProposalService;

class ProposalController extends Controller
{
    private ProposalService $proposalService;

    public function __construct()
    {
        $this->proposalService = ProposalLocator::proposalService();
    }

    public function create(ProposalCreateRequest $request): void
    {
        $this->proposalService->notify($request);
    }
}
