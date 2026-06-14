<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\HelpDesk;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\HelpDesk\TicketListResource;
use App\Http\Resources\Admin\HelpDesk\TicketResource;
use Core\App\HelpDesk\Ticket\DeleteCommand;
use Core\App\HelpDesk\Ticket\GetListCommand;
use Core\App\HelpDesk\Ticket\SaveCommand;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Account\AccountService;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Core\Domains\HelpDesk\Services\TicketService;
use Core\Domains\User\UserService;
use Core\Exceptions\ValidationException;
use Illuminate\Http\JsonResponse;
use lc;
use RuntimeException;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketService         $ticketService,
        private readonly GetListCommand        $getListCommand,
        private readonly SaveCommand           $saveCommand,
        private readonly DeleteCommand         $deleteCommand,
        private readonly TicketCategoryService $ticketCategoryService,
        private readonly TicketCatalogService  $ticketCatalogService,
        private readonly AccountService        $accountService,
        private readonly UserService           $userService,
    )
    {
    }

    // vue: resources/js/components/admin/help-desk/TicketsBlock.vue
    // vue: resources/js/components/admin/help-desk/TicketsList.vue
    public function list(DefaultRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::HELP_DESK_VIEW)) {
            abort(403);
        }

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
    // blade: resources/views/admin/pages/help-desk/view.blade.php
    // vue: resources/js/components/admin/help-desk/TicketsView.vue
    public function view(int $id)
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::HELP_DESK_VIEW)) {
            abort(403);
        }

        $ticket = $this->ticketService->getById($id);
        if ( ! $ticket) {
            abort(404);
        }

        $categories = $this->ticketCategoryService->search()->getItems();
        $services   = $this->ticketCatalogService->search()->getItems();
        $accounts   = $this->accountService->search()->getItems();
        $users      = $this->userService->search()->getItems();

        return view('pages.admin.help-desk.view', compact('ticket', 'categories', 'services', 'accounts', 'users'));
    }

    /**
     * @throws ValidationException
     */
    // vue: resources/js/components/admin/help-desk/TicketsView.vue
    // vue: resources/js/components/admin/help-desk/tickets-view/useTicketsView.js
    public function save(DefaultRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::HELP_DESK_EDIT)) {
            abort(403);
        }

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
            $request->getIntOrNull('user_id'),
            $request->getIntOrNull('account_id'),
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
    // vue: resources/js/components/admin/help-desk/TicketsView.vue
    public function delete(int $id): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::HELP_DESK_DROP)) {
            abort(403);
        }

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
