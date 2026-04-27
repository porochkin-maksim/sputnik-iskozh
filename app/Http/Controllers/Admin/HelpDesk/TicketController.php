<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\HelpDesk;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\HelpDesk\TicketListResource;
use App\Http\Resources\Admin\HelpDesk\TicketResource;
use App\Models\HelpDesk\Ticket;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\HelpDesk\Enums\TicketPriorityEnum;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Domains\HelpDesk\Searchers\TicketSearcher;
use Core\Domains\HelpDesk\Services\TicketService;
use Core\Domains\HelpDesk\UseCases\Ticket\DeleteUseCase;
use Core\Domains\HelpDesk\UseCases\Ticket\UpdateInputDTO;
use Core\Domains\HelpDesk\UseCases\Ticket\UpdateUseCase;
use Core\Exceptions\ValidationException;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class TicketController extends Controller
{
    private readonly TicketService $ticketService;

    public function __construct()
    {
        $this->ticketService = HelpDeskServiceLocator::TicketService();
    }

    public function list(DefaultRequest $request): JsonResponse
    {
        $searcher = new TicketSearcher();
        $searcher
            ->setLimit($request->getLimit())
            ->setOffset($request->getOffset())
        ;

        if ($request->getSortField() && $request->getSortOrder()) {
            $searcher->setSortOrderProperty(
                $request->getSortField(),
                $request->getSortOrder() === 'asc' ? SearcherInterface::SORT_ORDER_ASC : SearcherInterface::SORT_ORDER_DESC,
            );
        }
        else {
            $searcher->setSortOrderProperty(Ticket::ID, SearcherInterface::SORT_ORDER_DESC);
        }

        if ($request->getIntOrNull('category')) {
            $searcher->setCategoryId($request->getIntOrNull('category'));
        }

        if ($request->getIntOrNull('service')) {
            $searcher->setServiceId($request->getIntOrNull('service'));
        }

        if ($request->getIntOrNull('priority')) {
            $searcher->setPriority(TicketPriorityEnum::tryFrom($request->getInt('priority')));
        }

        if ($request->getIntOrNull('status')) {
            $searcher->setStatus(TicketStatusEnum::tryFrom($request->getInt('status')));
        }

        $searchResult = $this->ticketService->search($searcher);

        return response()->json([
            'tickets' => new TicketListResource($searchResult->getItems()),
            'total'   => $searchResult->getTotal(),
        ]);
    }

    /**
     * Получить одну категорию по ID
     */
    public function view(int $id)
    {
        $ticket = $this->ticketService->getById($id);
        if ( ! $ticket) {
            abort(404);
        }

        return view('admin.pages.help-desk.view', compact('ticket'));
    }

    /**
     * Создать новую категорию
     */
    public function save(DefaultRequest $request): JsonResponse
    {
        $dto = $this->ticketService->getById($request->getInt('id'));

        if ( ! $dto) {
            return response()->json([
                'success' => false,
                'message' => 'Заявка не найдена',
            ], 404);
        }

        $dto = new UpdateInputDTO(
            id          : $request->getInt('id'),
            description : $request->getString('description'),
            result      : $request->getStringOrNull('result'),
            type        : $request->getInt('type'),
            categoryId  : $request->getInt('category_id'),
            serviceId   : $request->getInt('service_id'),
            priority    : $request->getInt('priority'),
            status      : $request->getInt('status'),
            contactName : $request->getStringOrNull('contact_name'),
            contactPhone: $request->getStringOrNull('contact_phone'),
            contactEmail: $request->getStringOrNull('contact_email'),
            userId      : $request->getInt('user_id'),
            accountId   : $request->getInt('account_id'),
            files       : $request->file('files', []),
            resultFiles : $request->file('result_files', []),
        );

        try {
            $result = new UpdateUseCase()->execute($dto);
        }
        catch (ValidationException $e) {
            throw \Illuminate\Validation\ValidationException::withMessages($e->errors);
        }


        return response()->json([
            'success' => true,
            'ticket'  => new TicketResource($result),
            'message' => 'Заявка успешно обновлена',
        ]);
    }

    /**
     * Удалить категорию
     */
    public function delete(int $id): JsonResponse
    {
        $ticket = $this->ticketService->getById($id);

        if ( ! $ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Заявка не найдена',
            ], 404);
        }
        try {
            new DeleteUseCase()->execute($ticket);

            return response()->json([
                'success' => true,
                'message' => 'Заявка успешно удалена',
            ]);

        }
        catch (RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);

        }
    }
}
