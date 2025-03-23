<?php declare(strict_types=1);

namespace App\Http\Controllers\Pages\Files;

use App\Http\Controllers\Controller;
use App\Models\File\Folder;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\File\FolderLocator;
use Core\Domains\File\Requests\Folder\SaveRequest;
use Core\Domains\File\Requests\Folder\SearchRequest;
use Core\Domains\File\Services\FolderService;
use Core\Resources\Views\ViewNames;
use Core\Responses\ResponsesEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class FolderController extends Controller
{
    private FolderService $folderService;

    public function __construct()
    {
        $this->folderService = FolderLocator::FolderService();
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
        $searcher->setSortOrderProperty(Folder::NAME, SearcherInterface::SORT_ORDER_ASC);

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
        return \lc::roleDecorator()->canFiles();
    }
}
