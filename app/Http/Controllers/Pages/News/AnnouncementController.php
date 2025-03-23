<?php declare(strict_types=1);

namespace App\Http\Controllers\Pages\News;

use App\Http\Controllers\Controller;
use App\Models\News;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\News\Enums\CategoryEnum;
use Core\Domains\News\Factories\NewsFactory;
use Core\Domains\News\NewsLocator;
use Core\Domains\News\Requests\SearchRequest;
use Core\Domains\News\Services\NewsService;
use Core\Enums\DateTimeFormat;
use Core\Resources\Views\ViewNames;
use Core\Responses\ResponsesEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    private NewsService $newsService;
    private NewsFactory $newsFactory;

    public function __construct()
    {
        $this->newsService = NewsLocator::NewsService();
        $this->newsFactory = NewsLocator::NewsFactory();
    }

    public function index(): View
    {
        return view(ViewNames::PAGES_ANNOUNCEMENT_INDEX);
    }

    public function create(): JsonResponse
    {
        $news = $this->newsFactory->makeDefault()
            ->setCategory(CategoryEnum::ANNOUNCEMENT);

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

        if ($news->getCategory() !== CategoryEnum::ANNOUNCEMENT) {
            return redirect($news->url());
        }

        return view(ViewNames::PAGES_NEWS_SHOW, compact('news', 'edit'));
    }

    public function list(SearchRequest $request): JsonResponse
    {
        $canEdit = $this->canEdit();

        $searcher = $request->searcher();
        $searcher
            ->setSortOrderProperty(News::PUBLISHED_AT, SearcherInterface::SORT_ORDER_DESC)
            ->setCategory(CategoryEnum::ANNOUNCEMENT)
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

    private function canEdit(): bool
    {
        return \lc::roleDecorator()->canNews();
    }
}
