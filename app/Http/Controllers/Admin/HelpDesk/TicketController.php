<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\HelpDesk;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\HelpDesk\TicketListResource;
use App\Http\Resources\Admin\HelpDesk\TicketResource;
use Core\App\HelpDesk\Ticket\DeleteCommand;
use Core\App\HelpDesk\Ticket\GetListCommand;
use Core\App\HelpDesk\Ticket\SaveCommand;
use Core\Domains\HelpDesk\Services\TicketService;
use Core\Exceptions\ValidationException;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketService  $ticketService,
        private readonly GetListCommand $getListCommand,
        private readonly SaveCommand    $saveCommand,
        private readonly DeleteCommand  $deleteCommand,
    )
    {
    }

    public function list(DefaultRequest $request): JsonResponse
    {
        $searchResult = $this->getListCommand->execute(
            $request->getLimit(),
            $request->getOffset(),
            $request->getSortField(),
            $request->getSortOrder(),
            $request->getIntOrNull('category'),
            $request->getIntOrNull('service'),
            $request->getIntOrNull('priority'),
            $request->getIntOrNull('status'),
        );

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
     * @throws ValidationException
     */
    public function save(DefaultRequest $request): JsonResponse
    {
        $result = $this->saveCommand->execute(
            $request->getInt('id'),
            $request->getString('description'),
            $request->getStringOrNull('result'),
            $request->getInt('type'),
            $request->getInt('category_id'),
            $request->getInt('service_id'),
            $request->getInt('priority'),
            $request->getInt('status'),
            $request->getStringOrNull('contact_name'),
            $request->getStringOrNull('contact_phone'),
            $request->getStringOrNull('contact_email'),
            $request->getInt('user_id'),
            $request->getInt('account_id'),
            $request->files('files', []),
            $request->files('result_files', []),
        );

        if ($result === null) {
            return response()->json([
                'success' => false,
                'message' => 'Заявка не найдена',
            ], 404);
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
            $this->deleteCommand->execute($ticket);

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
