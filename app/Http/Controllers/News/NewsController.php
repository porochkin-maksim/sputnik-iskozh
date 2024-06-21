<?php declare(strict_types=1);

namespace App\Http\Controllers\News;

use App\Http\Controllers\Controller;
use App\Models\News;
use Core\Domains\Access\Enums\Permission;
use Core\Domains\Access\RoleLocator;
use Core\Domains\Access\Services\RoleService;
use Core\Domains\File\Enums\TypeEnum;
use Core\Domains\File\Requests\SaveRequest as SaveFileRequest;
use Core\Domains\News\Factories\NewsFactory;
use Core\Domains\News\NewsLocator;
use Core\Domains\News\Requests\SaveRequest;
use Core\Domains\News\Requests\SearchRequest;
use Core\Domains\News\Services\FileService;
use Core\Domains\News\Services\NewsService;
use Core\Resources\Views\ViewNames;
use Core\Responses\ResponsesEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    private NewsService $newsService;
    private NewsFactory $newsFactory;
    private FileService $fileService;
    private RoleService $roleService;

    public function __construct()
    {
        $this->newsService = NewsLocator::NewsService();
        $this->newsFactory = NewsLocator::NewsFactory();
        $this->fileService = NewsLocator::FileService();
        $this->roleService = RoleLocator::RoleService();
    }

    public function index(): View
    {
        return view(ViewNames::PAGES_NEWS_INDEX);
    }

    public function create(): JsonResponse
    {
        $news = $this->newsFactory->makeDefault();

        return response()->json([
            ResponsesEnum::NEWS => $news,
        ]);
    }

    public function show(int $id): View
    {
        $news = $this->newsService->getById($id);
        $edit = Auth::id();

        if ( ! $news) {
            abort(404);
        }

        return view(ViewNames::PAGES_NEWS_SHOW, compact('news', 'edit'));
    }

    public function edit(int $id): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }
        $news = $this->newsService->getById($id);

        return response()->json([
            ResponsesEnum::NEWS => $news,
            ResponsesEnum::EDIT => $this->canEdit(),
        ]);
    }

    public function list(SearchRequest $request): JsonResponse
    {
        $searcher = $request->dto();
        $searcher
            ->setSortOrderProperty(News::PUBLISHED_AT)
            ->setSortOrderDesc()
            ->setWithFiles();

        $news = $this->newsService->search($searcher);

        return response()->json([
            ResponsesEnum::NEWS  => $news->getItems(),
            ResponsesEnum::TOTAL => $news->getTotal(),
            ResponsesEnum::EDIT  => $this->canEdit(),
        ]);
    }

    public function save(SaveRequest $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }
        $news = $request->dto();
        $news = $this->newsService->save($news);

        return response()->json($news);
    }

    public function delete(int $id): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }
        return response()->json($this->newsService->deleteById($id));
    }

    /** Работа с файлами */

    public function uploadFile(int $id, Request $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }
        foreach ($request->allFiles() as $file) {
            $this->fileService->store($file, $id);
        }

        return response()->json(true);
    }

    public function saveFile(SaveFileRequest $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }
        $file = $this->fileService->getById($request->getId());
        if ($file && $file->getType() === TypeEnum::NEWS) {
            $file->setName($request->getName());
            $this->fileService->save($file);

            return response()->json(true);
        }

        return response()->json(false);
    }

    public function deleteFile(int $id): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }
        $file = $this->fileService->getById($id);
        if ($file?->getType() !== TypeEnum::NEWS) {
            abort(403);
        }

        return response()->json($this->fileService->deleteById($id));
    }

    private function canEdit(): bool
    {
        $role = $this->roleService->getByUserId(Auth::id());

        return Permission::canEditNews($role);
    }
}
