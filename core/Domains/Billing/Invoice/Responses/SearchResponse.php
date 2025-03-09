<?php declare(strict_types=1);

namespace Core\Domains\Billing\Invoice\Responses;

use Core\Db\Searcher\Models\SearchResponse as BaseSearchResponse;
use Core\Domains\Billing\Invoice\Collections\InvoiceCollection;

/**
 * @method InvoiceCollection getItems()
 */
class SearchResponse extends BaseSearchResponse
{

}
