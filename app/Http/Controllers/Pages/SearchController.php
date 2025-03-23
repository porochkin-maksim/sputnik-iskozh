<?php declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\News;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\File\FileLocator;
use Core\Domains\File\Models\FileSearcher;
use Core\Domains\File\Responses\FileSearchResponse;
use Core\Domains\File\Services\FileService;
use Core\Domains\News\Models\NewsSearcher;
use Core\Domains\News\NewsLocator;
use Core\Domains\News\Services\NewsService;
use Core\Enums\DateTimeFormat;
use Core\Responses\ResponsesEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    private const LIMIT = 10;

    private NewsService $newsService;
    private FileService $fileService;

    public function __construct()
    {
        $this->newsService = NewsLocator::NewsService();
        $this->fileService = FileLocator::FileService();
    }

    public function search(Request $request): JsonResponse
    {
        $result = [];

        $searching = $request->get('q');

        if ($searching) {
            $result[ResponsesEnum::NEWS]  = $this->searchNews($searching)->getItems();
            $result[ResponsesEnum::FILES] = $this->searchFiles($searching)->getItems();
        }

        return response()->json($result);
    }

    private function searchNews(string $searching): \Core\Domains\News\Responses\SearchResponse
    {
        $canEdit = \lc::roleDecorator()->canNews();

        $searcher = new NewsSearcher();
        $searcher->setLimit(self::LIMIT);
        $searcher->setSearch($searching);

        $ids = $this->newsService->getIdsByFullTextSearch($searcher);

        $searcher
            ->setIds($ids)
            ->setSortOrderProperty(News::PUBLISHED_AT, SearcherInterface::SORT_ORDER_DESC);

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
