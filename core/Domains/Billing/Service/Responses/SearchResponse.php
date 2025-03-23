<?php declare(strict_types=1);

namespace Core\Domains\Billing\Service\Responses;

use Core\Db\Searcher\Models\SearchResponse as BaseSearchResponse;
use Core\Domains\Billing\Service\Collections\ServiceCollection;

/**
 * @method ServiceCollection getItems()
 */
class SearchResponse extends BaseSearchResponse
{

}
