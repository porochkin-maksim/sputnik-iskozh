<?php declare(strict_types=1);

namespace Core\Domains\Counter\Responses;

use Core\Db\Searcher\Models\BaseSearchResponse;
use Core\Domains\Counter\Collections\CounterHistoryCollection;

/**
 * @method CounterHistoryCollection getItems()
 */
class CounterHistorySearchResponse extends BaseSearchResponse
{

}
