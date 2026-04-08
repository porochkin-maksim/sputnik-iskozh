<?php declare(strict_types=1);

namespace App\Http\Controllers\Public\HelpDesk;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\HelpDesk\Services\TicketServiceService;
use Core\Domains\HelpDesk\UseCases\Ticket\CreateInputDTO;
use Core\Domains\HelpDesk\UseCases\Ticket\CreateUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class HelpDeskController extends Controller
{
    private TicketCategoryService $ticketCategoryService;
    private TicketServiceService  $ticketServiceService;

    public function __construct()
    {
        $this->ticketCategoryService = HelpDeskServiceLocator::TicketCategoryService();
        $this->ticketServiceService  = HelpDeskServiceLocator::TicketServiceService();
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
        $input = new CreateInputDTO(
            typeCode    : $typeCode,
            categoryCode: $categoryCode,
            serviceCode : $serviceCode,
            description : $request->getString('description'),
            contactName : $request->getStringOrNull('name'),
            contactEmail: $request->getStringOrNull('email'),
            contactPhone: $request->getStringOrNull('phone'),
            accountId   : $request->getIntOrNull('account_id'),
            userId      : Auth::id(),
            files       : $request->file('files', []),
        );

        try {
            $ticket = new CreateUseCase()->execute($input);

            return response()->json(['success' => true, 'message' => sprintf('Заявка %s успешно создана', $ticket->getId()), 'number' => $ticket->getId()]);
        }
        catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }
        catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
        catch (\Throwable $e) {
            \Log::error($e);

            return response()->json(['success' => false, 'message' => 'Ошибка сервера'], 500);
        }
    }
}
