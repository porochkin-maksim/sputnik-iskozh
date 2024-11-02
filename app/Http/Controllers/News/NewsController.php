<?php declare(strict_types=1);

namespace App\Http\Controllers\News;

use App\Http\Controllers\Controller;
use App\Models\News;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\File\Enums\TypeEnum;
use Core\Domains\File\Requests\File\SaveRequest as SaveFileRequest;
use Core\Domains\News\Enums\CategoryEnum;
use Core\Domains\News\Factories\NewsFactory;
use Core\Domains\News\NewsLocator;
use Core\Domains\News\Requests\SaveRequest;
use Core\Domains\News\Requests\SearchRequest;
use Core\Domains\News\Services\FileService;
use Core\Domains\News\Services\NewsService;
use Core\Enums\DateTimeFormat;
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
            ResponsesEnum::NEWS       => $news,
            ResponsesEnum::CATEGORIES => CategoryEnum::json(),
        ]);
    }

    public function show(int $id): mixed
    {
        $news = $this->newsService->getById($id);
        $edit = Auth::id();

        if ( ! $news) {
            abort(404);
        }

        if ($news->getCategory() !== CategoryEnum::DEFAULT) {
            return redirect($news->url());
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
            ResponsesEnum::NEWS       => $news,
            ResponsesEnum::CATEGORIES => CategoryEnum::json(),
            ResponsesEnum::EDIT       => $this->canEdit(),
        ]);
    }

    public function list(SearchRequest $request): JsonResponse
    {
        $canEdit = $this->canEdit();

        $searcher = $request->searcher();
        $searcher
            ->setSortOrderProperty(News::PUBLISHED_AT, SearcherInterface::SORT_ORDER_DESC)
            ->setCategory(CategoryEnum::DEFAULT)
            ->setWithFiles();

        if ( ! $canEdit) {
            $searcher->addWhere(News::PUBLISHED_AT, SearcherInterface::LTE, now()->format(DateTimeFormat::DATE_TIME_DEFAULT));
        }

        $news = $this->newsService->search($searcher);

        return response()->json([
            ResponsesEnum::NEWS  => $news->getItems(),
            ResponsesEnum::TOTAL => $news->getTotal(),
            ResponsesEnum::EDIT  => $canEdit,
        ]);
    }

    public function lockedNews(SearchRequest $request): JsonResponse
    {
        $searcher = $request->searcher();
        $searcher
            ->setSelect([News::TITLE, News::ID, News::CATEGORY, News::PUBLISHED_AT,])
            ->setSortOrderProperty(News::PUBLISHED_AT, SearcherInterface::SORT_ORDER_DESC)
            ->addWhere(News::IS_LOCK, SearcherInterface::EQUALS, true);

        if ( ! $this->canEdit()) {
            $searcher->addWhere(News::PUBLISHED_AT, SearcherInterface::LTE, now()->format(DateTimeFormat::DATE_TIME_DEFAULT));
        }

        $news = $this->newsService->search($searcher);

        return response()->json([
            ResponsesEnum::NEWS  => $news->getItems(),
            ResponsesEnum::TOTAL => $news->getTotal(),
            ResponsesEnum::EDIT  => false,
        ]);

    }

    public function listAll(SearchRequest $request): JsonResponse
    {
        $searcher = $request->searcher();
        $searcher
            ->setSortOrderProperty(News::PUBLISHED_AT, SearcherInterface::SORT_ORDER_DESC)
            ->setWithFiles();

        if ( ! $this->canEdit()) {
            $searcher->addWhere(News::PUBLISHED_AT, SearcherInterface::LTE, now()->format(DateTimeFormat::DATE_TIME_DEFAULT));
        }

        $news = $this->newsService->search($searcher);

        return response()->json([
            ResponsesEnum::NEWS  => $news->getItems(),
            ResponsesEnum::TOTAL => $news->getTotal(),
            ResponsesEnum::EDIT  => false,
        ]);
    }

    public function save(SaveRequest $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        $news = $this->newsFactory->makeDefault()
            ->setId($request->getId())
            ->setTitle($request->getTitle())
            ->setDescription($request->getDescription())
            ->setArticle($request->getArticle())
            ->setCategory($request->getCategory())
            ->setIsLock($request->isLock())
            ->setPublishedAt($request->getPublishedAt());

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
        return \app::roleDecorator()->canEditNews();
    }
}
