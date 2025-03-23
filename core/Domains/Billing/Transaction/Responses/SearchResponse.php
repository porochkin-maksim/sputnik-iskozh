<?php declare(strict_types=1);

namespace Core\Domains\Billing\Transaction\Responses;

use Core\Db\Searcher\Models\SearchResponse as BaseSearchResponse;
use Core\Domains\Billing\Transaction\Collections\TransactionCollection;

/**
 * @method TransactionCollection getItems()
 */
class SearchResponse extends BaseSearchResponse
{

}
