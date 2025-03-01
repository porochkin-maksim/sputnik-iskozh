<?php declare(strict_types=1);

namespace Core\Domains\Account\Responses;

use Core\Db\Searcher\Models\SearchResponse as BaseSearchResponse;
use Core\Domains\Account\Collections\AccountCollection;

/**
 * @method AccountCollection getItems()
 */
class SearchResponse extends BaseSearchResponse
{

}
