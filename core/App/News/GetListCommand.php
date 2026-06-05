<?php declare(strict_types=1);

namespace Core\App\News;

use App\Models\News;
use Core\Domains\News\NewsCategoryEnum;
use Core\Domains\News\NewsSearcher;
use Core\Domains\News\NewsSearchResponse;
use Core\Domains\News\NewsService;
use Core\Exceptions\ValidationException;
use Core\Repositories\SearcherInterface;
use Core\Shared\Helpers\DateTime\DateTimeFormat;

readonly class GetListCommand
{
    public function __construct(
        private NewsService      $newsService,
        private GetListValidator $validator,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(
        ?int              $limit,
        ?int              $offset,
        ?string           $search,
        ?NewsCategoryEnum $category = null,
        bool              $withFiles = false,
        ?bool             $isLocked = null,
        bool              $onlyPublished = true,
    ): NewsSearchResponse
    {
        $this->validator->validate($limit, $offset, $search);

        $searcher = new NewsSearcher()
            ->setLimit($limit)
            ->setOffset($offset)
            ->setSearch($search)
            ->setSortOrderProperty(News::PUBLISHED_AT, SearcherInterface::SORT_ORDER_DESC)
        ;

        if ($category) {
            $searcher->setCategory($category);
        }

        if ($withFiles) {
            $searcher->setWithFiles();
        }

        if ($isLocked !== null) {
            $searcher->addWhere(News::IS_LOCK, SearcherInterface::EQUALS, $isLocked);
        }

        if ($onlyPublished) {
            $searcher->addWhere(
                News::PUBLISHED_AT,
                SearcherInterface::LTE,
                now()->format(DateTimeFormat::DATE_TIME_DEFAULT),
            );
        }

        return $this->newsService->search($searcher);
    }
}
