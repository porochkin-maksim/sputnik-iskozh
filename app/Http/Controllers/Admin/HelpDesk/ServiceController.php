<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\HelpDesk;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\HelpDesk\ServiceListResource;
use App\Http\Resources\Admin\HelpDesk\ServiceResource;
use Core\Domains\HelpDesk\Factories\TicketServiceFactory;
use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Domains\HelpDesk\Models\TicketServiceDTO;
use Core\Domains\HelpDesk\Searchers\TicketServiceSearcher;
use Core\Domains\HelpDesk\Services\TicketServiceService;
use Core\Domains\HelpDesk\UseCases\Service\CreateUseCase;
use Core\Domains\HelpDesk\UseCases\Service\DeleteUseCase;
use Core\Domains\HelpDesk\UseCases\Service\UpdateUseCase;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    private readonly TicketServiceService $ticketServiceService;
    private readonly TicketServiceFactory $ticketServiceFactory;

    public function __construct()
    {
        $this->ticketServiceService = HelpDeskServiceLocator::TicketServiceService();
        $this->ticketServiceFactory = HelpDeskServiceLocator::TicketServiceFactory();
    }

    public function list(int $categoryId): JsonResponse
    {
        $services = $this->ticketServiceService->search(
            new TicketServiceSearcher()
                ->setCategoryId($categoryId)
                ->useOrderSort(),
        )->getItems();

        return response()->json([
            'services' => new ServiceListResource($services),
        ]);
    }

    public function create(int $categoryId): JsonResponse
    {
        $lastService = $this->ticketServiceService->search(
            new TicketServiceSearcher()
                ->setCategoryId($categoryId)
                ->setSortOrderPropertyIdDesc()
                ->setLimit(1),
        )->getItems()->first();

        $service = $this->ticketServiceFactory->makeDefault()
            ->setCategoryId($categoryId)
            ->setSortOrder((int) $lastService?->getSortOrder() + 10)
            ->setIsActive(true)
        ;

        return response()->json([
            'service' => new ServiceResource($service),
        ]);
    }

    /**
     * Создать новую категорию
     */
    public function save(DefaultRequest $request): JsonResponse
    {
        if ($request->getInt('id')) {
            $dto = $this->ticketServiceService->getById($request->getInt('id'));

            if ( ! $dto) {
                return response()->json([
                    'success' => false,
                    'message' => 'Услуга не найдена',
                ], 404);
            }

            $dto
                ->setName($request->getString('name', $dto->getName()))
                ->setCode($request->getString('code', $dto->getCode()))
                ->setSortOrder($request->getInt('sort_order', $dto->getSortOrder()))
                ->setIsActive($request->getBool('is_active', $dto->getIsActive()))
            ;

            $result = new UpdateUseCase()->execute($dto);

            return response()->json([
                'success' => true,
                'service' => new ServiceResource($result),
                'message' => 'Услуга успешно обновлена',
            ]);
        }

        $dto = new TicketServiceDTO()
            ->setCategoryId($request->getInt('category_id'))
            ->setName($request->getString('name'))
            ->setCode($request->getString('code'))
            ->setSortOrder($request->getInt('sort_order', 0))
            ->setIsActive($request->getBool('is_active') ?? true)
        ;

        $result = new CreateUseCase()->execute($dto);

        return response()->json([
            'success' => true,
            'service' => new ServiceResource($result),
            'message' => 'Услуга успешно создана',
        ], 201);
    }

    /**
     * Удалить категорию
     */
    public function delete(int $id): JsonResponse
    {
        $service = $this->ticketServiceService->getById($id);

        if ( ! $service) {
            return response()->json([
                'success' => false,
                'message' => 'Услуга не найдена',
            ], 404);
        }

        new DeleteUseCase()->execute($service);

        return response()->json([
            'success' => true,
            'message' => 'Услуга успешно удалена',
        ]);
    }
}
