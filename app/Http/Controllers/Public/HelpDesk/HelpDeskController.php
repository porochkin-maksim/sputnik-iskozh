<?php declare(strict_types=1);

namespace App\Http\Controllers\Public\HelpDesk;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\PublicConsentRequest;
use App\Http\Resources\Profile\Accounts\AccountResource;
use App\Http\Resources\Profile\Users\UserResource;
use App\Resources\RouteNames;
use Core\App\HelpDesk\Ticket\CreateCommand;
use Core\App\HelpDesk\Ticket\CreateInput;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use lc;
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
        $items = [];
        foreach (TicketTypeEnum::cases() as $ticketType) {
            $categories = $this->ticketCategoryService->getByType($ticketType);
            if ($categories->hasServices()) {
                $items[] = [
                    'href'  => route(RouteNames::HELP_DESK_TYPE, $ticketType->code()),
                    'title' => $ticketType->name(),
                    'icon'  => $ticketType->icon(),
                    'color' => $ticketType->color(),
                ];
            }
        }

        return view('pages.public.help-desk.index', compact('items'));
    }

    public function type(string $typeCode)
    {
        $type = TicketTypeEnum::byCode($typeCode);
        if ( ! $type) {
            abort(404);
        }

        $categories = $this->ticketCategoryService->getByType($type);

        return view('pages.public.help-desk.type', compact('type', 'categories'));
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

        $categories = $this->ticketCategoryService->getByType($type);

        return view('pages.public.help-desk.category', compact('type', 'category', 'categories'));
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

        $categories      = $this->ticketCategoryService->getByType($type);
        $userResource    = lc::user()->getId() ? new UserResource(lc::user()) : null;
        $accountResource = lc::account()->getId() ? new AccountResource(lc::account()) : null;

        return view('pages.public.help-desk.service', compact('type', 'category', 'service', 'categories', 'userResource', 'accountResource'));
    }

    public function ticket(PublicConsentRequest $request, string $typeCode, string $categoryCode, string $serviceCode): JsonResponse
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
