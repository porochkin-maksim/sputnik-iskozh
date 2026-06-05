<?php declare(strict_types=1);

namespace App\Http\Controllers\Public\Files;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Shared\Files\FolderResource;
use App\Http\Resources\Shared\ResourseList;
use App\Services\Files\Services\FolderBreadcrumbService;
use Core\App\Folders\DeleteCommand;
use Core\App\Folders\GetListCommand;
use Core\App\Folders\SaveCommand;
use Core\Domains\Folders\FolderService;
use Core\Exceptions\ValidationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class FolderController extends Controller
{
    public function __construct(
        private readonly FolderService           $folderService,
        private readonly GetListCommand          $getListCommand,
        private readonly SaveCommand             $saveCommand,
        private readonly DeleteCommand           $deleteCommand,
        private readonly FolderBreadcrumbService $folderBreadcrumbService,
    )
    {
    }

    public function index(?string $folderUid = null): View
    {
        $folder = null;
        if ($folderUid) {
            $folder = $this->folderService->getByUid($folderUid);
        }

        return view('pages.public.files.index', compact('folder'));
    }

    /**
     * @throws ValidationException
     */
    public function list(DefaultRequest $request): JsonResponse
    {
        $searchResult = $this->getListCommand->execute(
            $request->getIntOrNull('limit'),
            $request->getIntOrNull('parent_id'),
        );

        return response()->json([
            'folders' => new ResourseList($searchResult->getItems(), FolderResource::class),
            'edit'    => $this->canEdit(),
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function save(DefaultRequest $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        $folder = $this->saveCommand->execute(
            $request->getIntOrNull('id'),
            $request->getString('name'),
            $request->getIntOrNull('parent_id'),
        );

        return response()->json(new FolderResource($folder));
    }

    public function show(int|string|null $id): JsonResponse
    {
        $folder = $id ? $this->folderService->getById((int) $id) : null;

        return response()->json([
            'folder' => $folder ? new FolderResource($folder) : null,
            'edit'   => $this->canEdit(),
        ]);
    }

    public function info(int|null $id = null): ?JsonResponse
    {
        return response()->json([
            'breadcrumbs' => $this->folderBreadcrumbService->buildHtml($id),
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function delete(int|string|null $id): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        return response()->json($this->deleteCommand->execute((int) $id));
    }

    private function canEdit(): bool
    {
        return \lc::roleDecorator()->canFiles();
    }
}
