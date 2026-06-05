<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\HelpDesk;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\HelpDesk\CategoryListResource;
use App\Http\Resources\Admin\HelpDesk\CategoryResource;
use Core\App\HelpDesk\Category\CreateCommand;
use Core\App\HelpDesk\Category\DeleteCommand;
use Core\App\HelpDesk\Category\GetListCommand;
use Core\App\HelpDesk\Category\SaveCommand;
use Core\Domains\HelpDesk\Services\TicketCategoryService;
use Core\Exceptions\ValidationException;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class CategoryController extends Controller
{
    public function __construct(
        private readonly TicketCategoryService $ticketCategoryService,
        private readonly GetListCommand        $getListCommand,
        private readonly CreateCommand         $createCommand,
        private readonly SaveCommand           $saveCommand,
        private readonly DeleteCommand         $deleteCommand,
    )
    {
    }

    public function list(): JsonResponse
    {
        $categories = $this->getListCommand->execute();

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
        $category = $this->createCommand->execute($type);

        return response()->json([
            'category' => new CategoryResource($category),
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function save(DefaultRequest $request): JsonResponse
    {
        $isUpdate = $request->getIntOrNull('id') !== null;
        $result   = $this->saveCommand->execute(
            $request->getIntOrNull('id'),
            $request->getInt('type'),
            $request->getString('name'),
            $request->getString('code'),
            $request->getInt('sort_order', 0),
            $request->getBool('is_active', true),
        );

        if ($result === null) {
            return response()->json([
                'success' => false,
                'message' => 'Категория не найдена',
            ], 404);
        }

        return response()->json([
            'success'  => true,
            'category' => new CategoryResource($result),
            'message'  => $isUpdate ? 'Категория успешно обновлена' : 'Категория успешно создана',
        ], $isUpdate ? 200 : 201);
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
            $this->deleteCommand->execute($category);

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
