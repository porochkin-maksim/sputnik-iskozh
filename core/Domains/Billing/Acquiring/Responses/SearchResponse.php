<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Responses;

use Core\Db\Searcher\Models\SearchResponse as BaseSearchResponse;
use Core\Domains\Billing\Acquiring\Collections\AcquiringCollection;

/**
 * @method AcquiringCollection getItems()
 */
class SearchResponse extends BaseSearchResponse
{

}
