<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges\Responses;

use Core\Db\Searcher\Models\SearchResponse as BaseSearchResponse;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;

/**
 * @method HistoryChangesService getItems()
 */
class SearchResponse extends BaseSearchResponse
{

}
