<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim\Responses;

use Core\Db\Searcher\Models\SearchResponse as BaseSearchResponse;
use Core\Domains\Billing\Claim\Collections\ClaimCollection;

/**
 * @method ClaimCollection getItems()
 */
class SearchResponse extends BaseSearchResponse
{

}
