<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\HelpDesk;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\HelpDesk\CategoryListResource;
use App\Http\Resources\Admin\HelpDesk\CategoryResource;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Core\Domains\HelpDesk\Factories\TicketCategoryFactory;
use Core\Domains\HelpDesk\HelpDeskServiceLocator;
use Core\Domains\HelpDesk\Models\TicketCategoryDTO;
use Core\Domains\HelpDesk\Searchers\TicketCategorySearcher;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Domains\HelpDesk\UseCases\Category\CreateUseCase;
use Core\Domains\HelpDesk\UseCases\Category\DeleteUseCase;
use Core\Domains\HelpDesk\UseCases\Category\UpdateUseCase;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class CategoryController extends Controller
{
    private readonly TicketCategoryService $ticketCategoryService;
    private readonly TicketCategoryFactory $ticketCategoryFactory;

    public function __construct()
    {
        $this->ticketCategoryService = HelpDeskServiceLocator::TicketCategoryService();
        $this->ticketCategoryFactory = HelpDeskServiceLocator::TicketCategoryFactory();
    }

    public function list(): JsonResponse
    {
        $categories = $this->ticketCategoryService->search(
            new TicketCategorySearcher()
                ->useOrderSort(),
        )->getItems();

        return response()->json([
            'categories' => new CategoryListResource($categories),
        ]);
    }

    /**
     * Получить одну категорию по ID
     */
    public function get(int $id): JsonResponse
    {
        $category = $this->ticketCategoryService->getById($id);

        if ( ! $category) {
            return response()->json([
                'success' => false,
                'message' => 'Категория не найдена',
            ], 404);
        }

        return response()->json([
            'success'  => true,
            'category' => new CategoryResource($category),
        ]);
    }

    public function create(int $type): JsonResponse
    {
        $lastCategory = $this->ticketCategoryService->search(
            new TicketCategorySearcher()
                ->setType(TicketTypeEnum::tryFrom($type))
                ->setSortOrderPropertyIdDesc()
                ->setLimit(1),
        )->getItems()->first();

        $category = $this->ticketCategoryFactory->makeDefault()
            ->setSortOrder((int) $lastCategory?->getSortOrder() + 10)
        ;

        return response()->json([
            'category' => new CategoryResource($category),
        ]);
    }

    /**
     * Создать новую категорию
     */
    public function save(DefaultRequest $request): JsonResponse
    {
        if ($request->getInt('id')) {
            $dto = $this->ticketCategoryService->getById($request->getInt('id'));

            if ( ! $dto) {
                return response()->json([
                    'success' => false,
                    'message' => 'Категория не найдена',
                ], 404);
            }

            $dto->setType(TicketTypeEnum::tryFrom($request->getInt('type')))
                ->setName($request->getString('name'))
                ->setCode($request->getString('code'))
                ->setSortOrder($request->getInt('sort_order', 0))
                ->setIsActive($request->getBool('is_active') ?? true)
            ;

            $result = new UpdateUseCase()->execute($dto);

            return response()->json([
                'success'  => true,
                'category' => new CategoryResource($result),
                'message'  => 'Категория успешно обновлена',
            ]);
        }

        $dto = new TicketCategoryDTO()
            ->setType(TicketTypeEnum::tryFrom($request->getInt('type')))
            ->setName($request->getString('name'))
            ->setCode($request->getString('code'))
            ->setSortOrder($request->getInt('sort_order', 0))
            ->setIsActive($request->getBool('is_active') ?? true)
        ;

        $result = new CreateUseCase()->execute($dto);

        return response()->json([
            'success'  => true,
            'category' => new CategoryResource($result),
            'message'  => 'Категория успешно создана',
        ], 201);
    }

    /**
     * Удалить категорию
     */
    public function delete(int $id): JsonResponse
    {
        $category = $this->ticketCategoryService->getById($id);

        if ( ! $category) {
            return response()->json([
                'success' => false,
                'message' => 'Категория не найдена',
            ], 404);
        }
        try {
            new DeleteUseCase()->execute($category);

            return response()->json([
                'success' => true,
                'message' => 'Категория успешно удалена',
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
