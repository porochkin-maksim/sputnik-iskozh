<?php declare(strict_types=1);

namespace App\Http\Controllers\Public\HelpDesk;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use Core\App\HelpDesk\Ticket\CreateCommand;
use Core\App\HelpDesk\Ticket\CreateInput;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Throwable;

class HelpDeskController extends Controller
{
    public function __construct(
        private readonly TicketCategoryService $ticketCategoryService,
        private readonly TicketCatalogService  $ticketServiceService,
        private readonly CreateCommand         $createCommand,
    )
    {
    }

    public function index()
    {
        return view('public.help-desk.index');
    }

    public function type(string $typeCode)
    {
        $type = TicketTypeEnum::byCode($typeCode);
        if ( ! $type) {
            abort(404);
        }

        return view('public.help-desk.type', compact('type'));
    }

    public function category(string $typeCode, string $categoryCode)
    {
        $type = TicketTypeEnum::byCode($typeCode);
        if ( ! $type) {
            abort(404);
        }

        $category = $this->ticketCategoryService->findByTypeAndCode($type, $categoryCode);
        if ( ! $category || ! $category->getIsActive()) {
            abort(404);
        }

        return view('public.help-desk.category', compact('type', 'category'));
    }

    public function form(string $typeCode, string $categoryCode, string $serviceCode)
    {
        $type = TicketTypeEnum::byCode($typeCode);
        if ( ! $type) {
            abort(404);
        }

        $category = $this->ticketCategoryService->findByTypeAndCode($type, $categoryCode);
        if ( ! $category || ! $category->getIsActive()) {
            abort(404);
        }

        $service = $this->ticketServiceService->findByCategoryIdAndCode($category->getId(), $serviceCode);
        if ( ! $service || ! $service->getIsActive()) {
            abort(404);
        }

        return view('public.help-desk.service', compact('type', 'category', 'service'));
    }

    public function ticket(DefaultRequest $request, string $typeCode, string $categoryCode, string $serviceCode): JsonResponse
    {
        $input = new CreateInput(
            typeCode    : $typeCode,
            categoryCode: $categoryCode,
            serviceCode : $serviceCode,
            description : $request->getString('description'),
            contactName : $request->getStringOrNull('name'),
            contactEmail: $request->getStringOrNull('email'),
            contactPhone: $request->getStringOrNull('phone'),
            accountId   : $request->getIntOrNull('account_id'),
            userId      : Auth::id(),
            files       : $request->files('files', []),
        );

        try {
            $ticket = $this->createCommand->execute($input);

            return response()->json(['success' => true, 'message' => sprintf('Заявка %s успешно создана', $ticket->getId()), 'number' => $ticket->getId()]);
        }
        catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
        catch (Throwable $e) {
            \Log::error($e);

            return response()->json(['success' => false, 'message' => 'Ошибка сервера'], 500);
        }
    }
}
