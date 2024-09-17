<?php declare(strict_types=1);

namespace App\Http\Controllers\Polls;

use App\Http\Controllers\Controller;
use App\Models\Polls\Poll;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Access\Enums\Permission;
use Core\Domains\Access\RoleLocator;
use Core\Domains\Access\Services\RoleService;
use Core\Domains\Poll\Factories\PollFactory;
use Core\Domains\Poll\PollLocator;
use Core\Domains\Poll\Requests\SaveRequest;
use Core\Domains\Poll\Requests\SearchRequest;
use Core\Domains\Poll\Services\PollService;
use Core\Enums\DateTimeFormat;
use Core\Resources\Views\ViewNames;
use Core\Responses\ResponsesEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PollController extends Controller
{
    private PollService $pollService;
    private PollFactory $pollFactory;
    private RoleService $roleService;

    public function __construct()
    {
        $this->pollService = PollLocator::PollService();
        $this->pollFactory = PollLocator::PollFactory();
        $this->roleService = RoleLocator::RoleService();
    }

    public function index(): View
    {
        return view(ViewNames::PAGES_POLLS_INDEX);
    }

    public function create(): JsonResponse
    {
        $poll = $this->pollFactory->makeDefault();

        return response()->json([
            ResponsesEnum::POLL => $poll,
            ResponsesEnum::EDIT => $this->canEdit(),
        ]);
    }

    public function show(int $id): View
    {
        $poll = $this->pollService->getById($id);
        $edit = Auth::id();

        if ( ! $poll) {
            abort(404);
        }

        return view(ViewNames::PAGES_NEWS_SHOW, compact('poll', 'edit'));
    }

    public function edit(int $id): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }
        $poll = $this->pollService->getById($id);

        return response()->json([
            ResponsesEnum::POLL => $poll,
            ResponsesEnum::EDIT => $this->canEdit(),
        ]);
    }

    public function list(SearchRequest $request): JsonResponse
    {
        $canEdit = $this->canEdit();

        $searcher = $request->dto();
        $searcher
            ->setSortOrderProperty(Poll::ID, SearcherInterface::SORT_ORDER_DESC)
            ->setWithQuestions();

        if ( ! $canEdit) {
            $searcher->addWhere(Poll::start_at, SearcherInterface::LTE, now()->format(DateTimeFormat::DATE_TIME_DEFAULT));
        }

        $poll = $this->pollService->search($searcher);

        return response()->json([
            ResponsesEnum::POLLS => $poll->getItems(),
            ResponsesEnum::TOTAL => $poll->getTotal(),
            ResponsesEnum::EDIT  => $canEdit,
        ]);
    }

    public function save(SaveRequest $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        $poll = $this->pollFactory->makeDefault()
            ->setId($request->getId())
            ->setTitle($request->getTitle())
            ->setDescription($request->getDescription())
            ->setstartAt($request->getstartAt())
            ->setEndsAt($request->getEndsAt());

        $poll = $this->pollService->save($poll);

        return response()->json($poll);
    }

    public function delete(int $id): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        return response()->json($this->pollService->deleteById($id));
    }

    private function canEdit(): bool
    {
        $role = $this->roleService->getByUserId(Auth::id());

        return Permission::canEditPoll($role);
    }
}
