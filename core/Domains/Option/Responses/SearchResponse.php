<?php declare(strict_types=1);

namespace Core\Domains\Option\Responses;

use Core\Db\Searcher\Models\SearchResponse as BaseSearchResponse;
use Core\Domains\Option\Collections\OptionCollection;

/**
 * @method OptionCollection getItems()
 */
class SearchResponse extends BaseSearchResponse
{

}