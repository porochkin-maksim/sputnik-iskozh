<?php declare(strict_types=1);

namespace App\Http\Controllers\Files;

use App\Http\Controllers\Controller;
use App\Models\File\Folder;
use Core\Domains\Access\Enums\Permission;
use Core\Domains\Access\RoleLocator;
use Core\Domains\Access\Services\RoleService;
use Core\Domains\File\FolderLocator;
use Core\Domains\File\Requests\Folder\SaveRequest;
use Core\Domains\File\Requests\Folder\SearchRequest;
use Core\Domains\File\Services\FolderService;
use Core\Resources\Views\ViewNames;
use Core\Responses\ResponsesEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FolderController extends Controller
{
    private FolderService $folderService;
    private RoleService   $roleService;

    public function __construct()
    {
        $this->folderService = FolderLocator::FolderService();
        $this->roleService   = RoleLocator::RoleService();
    }

    public function index(?string $folderUid = null): View
    {
        $folder = null;
        if ($folderUid) {
            $folder = $this->folderService->getByUid($folderUid);
        }
        return view(ViewNames::PAGES_FILES_INDEX , compact('folder'));
    }

    public function list(SearchRequest $request): JsonResponse
    {
        $searcher = $request->dto();
        $searcher->setSortOrderProperty(Folder::NAME);

        $folders = $this->folderService->search($searcher)->getItems();

        return response()->json([
            ResponsesEnum::FOLDERS => $folders,
            ResponsesEnum::EDIT   => $this->canEdit(),
        ]);
    }

    public function save(SaveRequest $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        $folder = $request->dto();
        $folder = $this->folderService->save($folder);
        $folder = $this->folderService->getById($folder->getId());

        return response()->json($folder);
    }

    public function show(?int $id): JsonResponse
    {
        $folder = $id ? $this->folderService->getById($id) : null;

        return response()->json([
            ResponsesEnum::FOLDER => $folder,
            ResponsesEnum::EDIT   => $this->canEdit(),
        ]);
    }

    public function delete(int $id): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        return response()->json($this->folderService->deleteById($id));
    }

    private function canEdit(): bool
    {
        $role = $this->roleService->getByUserId(Auth::id());

        return Permission::canEditFiles($role);
    }
}
