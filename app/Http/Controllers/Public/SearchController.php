<?php declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\News;
use Core\Repositories\SearcherInterface;
use Core\Domains\Files\FileSearcher;
use Core\Domains\Files\FileSearchResponse;
use Core\Domains\Files\FileService;
use Core\Domains\News\NewsSearcher;
use Core\Domains\News\NewsSearchResponse;
use Core\Domains\News\NewsService;
use Core\Shared\Helpers\DateTime\DateTimeFormat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    private const int LIMIT = 10;

    public function __construct(
        private readonly FileService $fileService,
        private readonly NewsService $newsService,
    )
    {
    }

    public function search(Request $request): JsonResponse
    {
        $result = [];

        $searching = $request->input('q');

        if ($searching) {
            $result['news']  = $this->searchNews($searching)->getItems();
            $result['files'] = $this->searchFiles($searching)->getItems();
        }

        return response()->json($result);
    }

    private function searchNews(string $searching): NewsSearchResponse
    {
        $canEdit = \lc::roleDecorator()->canNews();

        $searcher = new NewsSearcher();
        $searcher->setLimit(self::LIMIT);
        $searcher->setSearch($searching);

        $ids = $this->newsService->getIdsByFullTextSearch($searcher);

        $searcher
            ->setIds($ids)
            ->setSortOrderProperty(News::PUBLISHED_AT, SearcherInterface::SORT_ORDER_DESC)
        ;

        if ( ! $canEdit) {
            $searcher->addWhere(News::PUBLISHED_AT, SearcherInterface::LTE, now()->format(DateTimeFormat::DATE_TIME_DEFAULT));
        }

        return $this->newsService->search($searcher);
    }

    private function searchFiles(string $searching): FileSearchResponse
    {
        $searcher = new FileSearcher();
        $searcher->setLimit(self::LIMIT);
        $searcher->setType(null);
        $searcher->setSearch($searching);

        $ids = $this->fileService->getIdsByFullTextSearch($searcher);

        $searcher = new FileSearcher();
        $searcher->setIds($ids);

        return $this->fileService->search($searcher);
    }
}
