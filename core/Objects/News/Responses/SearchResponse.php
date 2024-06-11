<?php declare(strict_types=1);

namespace Core\Objects\News\Responses;

use Core\Db\Searcher\Models\SearchResponse as BaseSearchResponse;
use Core\Objects\News\Collections\NewsCollection;

/**
 * @method NewsCollection getItems()
 */
class SearchResponse extends BaseSearchResponse
{

}
