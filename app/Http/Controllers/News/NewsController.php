<?php declare(strict_types=1);

namespace App\Http\Controllers\News;

use App\Http\Controllers\Controller;
use App\Models\News;
use Core\Objects\News\Factories\NewsFactory;
use Core\Objects\News\NewsLocator;
use Core\Objects\News\Requests\SaveRequest;
use Core\Objects\News\Requests\SearchRequest;
use Core\Objects\News\Services\FileService;
use Core\Objects\News\Services\NewsService;
use Core\Resources\ViewNames;
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

    public function __construct()
    {
        $this->newsService = NewsLocator::NewsService();
        $this->newsFactory = NewsLocator::NewsFactory();
        $this->fileService = NewsLocator::FileService();
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

        if (!$news) {
            abort(404);
        }
        return view(ViewNames::PAGES_NEWS_SHOW, compact('news', 'edit'));
    }

    public function edit(int $id): JsonResponse
    {
        $news = $this->newsService->getById($id);

        return response()->json([
            ResponsesEnum::NEWS => $news,
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
            ResponsesEnum::NEWS => $news,
            ResponsesEnum::EDIT => (bool) Auth::id(),
        ]);
    }

    public function save(SaveRequest $request): JsonResponse
    {
        $news = $request->dto();
        $news = $this->newsService->save($news);

        return response()->json($news);
    }

    public function delete(int $id): JsonResponse
    {
        return response()->json($this->newsService->deleteById($id));
    }

    public function uploadFile(int $id, Request $request): JsonResponse
    {
        foreach ($request->allFiles() as $file) {
            $this->fileService->save($file, $id);
        }

        return response()->json(true);
    }
}
