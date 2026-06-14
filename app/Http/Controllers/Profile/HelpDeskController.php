<?php declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Profile\HelpDesk\TicketListResource;
use Core\Domains\HelpDesk\Searchers\TicketCommentSearcher;
use Core\Domains\HelpDesk\Searchers\TicketSearcher;
use Core\Domains\HelpDesk\Services\TicketService;
use Core\Domains\HelpDesk\TicketCommentRepositoryInterface;
use Core\Repositories\SearcherInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use lc;

class HelpDeskController extends Controller
{
    public function __construct(
        private readonly TicketService                    $ticketService,
        private readonly TicketCommentRepositoryInterface $commentRepository,
    )
    {
    }

    public function index(): View
    {
        return view('pages.profile.help-desk.index');
    }

    public function view(string $id): View
    {
        $id        = (int) $id;
        $ticket    = $this->ticketService->getById($id);
        $accountId = lc::account()?->getId();

        if ( ! $ticket) {
            abort(404);
        }

        if ($ticket->getUserId() !== Auth::id() && $ticket->getAccountId() !== $accountId) {
            abort(404);
        }

        $commentSearcher = (new TicketCommentSearcher())
            ->addWhere('ticket_id', SearcherInterface::EQUALS, $id)
            ->addWhere('is_internal', SearcherInterface::EQUALS, false)
            ->setSortOrderProperty('created_at', SearcherInterface::SORT_ORDER_ASC);

        $comments = $this->commentRepository->search($commentSearcher)->getItems();

        return view('pages.profile.help-desk.view', compact('ticket', 'comments'));
    }

    public function list(DefaultRequest $request): JsonResponse
    {
        $searcher = (new TicketSearcher())
            ->setSortOrderProperty('id', SearcherInterface::SORT_ORDER_DESC)
            ->setLimit($request->getLimit() ?? 20)
            ->setOffset($request->getOffset());

        $accountId = lc::account()?->getId();

        if ($accountId) {
            $searcher->addWhere('account_id', SearcherInterface::EQUALS, $accountId);
        }
        else {
            $searcher->setUserId(Auth::id());
        }

        $result = $this->ticketService->search($searcher);

        return response()->json([
            'tickets' => new TicketListResource($result->getItems()),
            'total'   => $result->getTotal(),
            'limit'   => $request->getLimit() ?? 20,
        ]);
    }

}
