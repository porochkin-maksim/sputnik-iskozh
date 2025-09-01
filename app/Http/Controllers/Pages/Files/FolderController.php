<?php declare(strict_types=1);

namespace App\Http\Controllers\Pages\Files;

use App\Http\Controllers\Controller;
use App\Models\File\Folder;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\File\Collections\FolderCollection;
use Core\Domains\File\FolderLocator;
use Core\Domains\File\Requests\Folder\SaveRequest;
use Core\Domains\File\Requests\Folder\SearchRequest;
use Core\Domains\File\Services\FolderService;
use Core\Resources\RouteNames;
use Core\Resources\Views\ViewNames;
use Core\Responses\ResponsesEnum;
use Diglactic\Breadcrumbs\Breadcrumbs;
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

        return view(ViewNames::PAGES_FILES_INDEX, compact('folder'));
    }

    public function list(SearchRequest $request): JsonResponse
    {
        $searcher = $request->dto();
        $searcher->setSortOrderProperty(Folder::NAME, SearcherInterface::SORT_ORDER_ASC);

        $folders = $this->folderService->search($searcher)->getItems();

        return response()->json([
            ResponsesEnum::FOLDERS => $folders,
            ResponsesEnum::EDIT    => $this->canEdit(),
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

    public function show(int|string|null $id): JsonResponse
    {
        $folder = $id ? $this->folderService->getById((int) $id) : null;

        return response()->json([
            ResponsesEnum::FOLDER => $folder,
            ResponsesEnum::EDIT   => $this->canEdit(),
        ]);
    }

    public function info(int|string|null $id = null): ?JsonResponse
    {
        $folders = ($id ? $this->folderService->getWithParentsRecursively($id) : new FolderCollection())->reverse();

        $breadcrumbs = Breadcrumbs::generate(RouteNames::FILES);
        foreach ($folders as $folder) {
            $item        = new \stdClass();
            $item->title = $folder->getName();
            $item->url   = $folder->getUrl();
            $breadcrumbs->add($item);
        }

        $navHtmlList = '';

        foreach ($breadcrumbs as $index => $breadcrumb) {
            $isActive    = $index === count($breadcrumbs) - 1 ? ' active' : '';
            $navHtmlList .= <<<HTML
                <li class="breadcrumb-item{$isActive}">
                    <a href="{$breadcrumb->url}">{$breadcrumb->title}</a>
                </li>
                HTML;
        }

        return response()->json([
            'breadcrumbs' => $navHtmlList,
        ]);
    }

    public function delete(int|string|null $id): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        return response()->json($this->folderService->deleteById((int) $id));
    }

    private function canEdit(): bool
    {
        return \lc::roleDecorator()->canFiles();
    }
}
