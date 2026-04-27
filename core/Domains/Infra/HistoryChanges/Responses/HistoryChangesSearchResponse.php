<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges\Responses;

use Core\Db\Searcher\Models\BaseSearchResponse;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;

/**
 * @method HistoryChangesService getItems()
 */
class HistoryChangesSearchResponse extends BaseSearchResponse
{

}
