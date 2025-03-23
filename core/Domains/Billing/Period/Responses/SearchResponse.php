<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period\Responses;

use Core\Db\Searcher\Models\SearchResponse as BaseSearchResponse;
use Core\Domains\Billing\Period\Collections\PeriodCollection;

/**
 * @method PeriodCollection getItems()
 */
class SearchResponse extends BaseSearchResponse
{

}
