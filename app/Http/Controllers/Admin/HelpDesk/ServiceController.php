<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\HelpDesk;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\HelpDesk\ServiceListResource;
use App\Http\Resources\Admin\HelpDesk\ServiceResource;
use Core\App\HelpDesk\Service\CreateCommand;
use Core\App\HelpDesk\Service\DeleteCommand;
use Core\App\HelpDesk\Service\GetListCommand;
use Core\App\HelpDesk\Service\SaveCommand;
use Core\Domains\HelpDesk\Services\TicketCatalogService;
use Core\Exceptions\ValidationException;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    public function __construct(
        private readonly TicketCatalogService $ticketServiceService,
        private readonly GetListCommand       $getListCommand,
        private readonly CreateCommand        $createCommand,
        private readonly SaveCommand          $saveCommand,
        private readonly DeleteCommand        $deleteCommand,
    )
    {
    }

    public function list(int $categoryId): JsonResponse
    {
        $services = $this->getListCommand->execute($categoryId);

        return response()->json([
            'services' => new ServiceListResource($services),
        ]);
    }

    public function create(int $categoryId): JsonResponse
    {
        $service = $this->createCommand->execute($categoryId);

        return response()->json([
            'service' => new ServiceResource($service),
        ]);
    }

    /**
     * Создать новую категорию
     */
    /**
     * @throws ValidationException
     */
    public function save(DefaultRequest $request): JsonResponse
    {
        $isUpdate = $request->getIntOrNull('id') !== null;
        $result   = $this->saveCommand->execute(
            $request->getIntOrNull('id'),
            $request->getInt('category_id'),
            $request->getString('name'),
            $request->getString('code'),
            $request->getInt('sort_order', 0),
            $request->getBool('is_active', true),
        );

        if ($result === null) {
            return response()->json([
                'success' => false,
                'message' => 'Услуга не найдена',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'service' => new ServiceResource($result),
            'message' => $isUpdate ? 'Услуга успешно обновлена' : 'Услуга успешно создана',
        ], $isUpdate ? 200 : 201);
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

        $this->deleteCommand->execute($service);

        return response()->json([
            'success' => true,
            'message' => 'Услуга успешно удалена',
        ]);
    }
}
