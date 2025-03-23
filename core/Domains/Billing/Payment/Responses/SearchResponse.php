<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment\Responses;

use Core\Db\Searcher\Models\SearchResponse as BaseSearchResponse;
use Core\Domains\Billing\Payment\Collections\PaymentCollection;

/**
 * @method PaymentCollection getItems()
 */
class SearchResponse extends BaseSearchResponse
{

}
