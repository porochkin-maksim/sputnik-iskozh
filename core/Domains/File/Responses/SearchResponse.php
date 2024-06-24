<?php declare(strict_types=1);

namespace Core\Domains\File\Responses;

use Core\Db\Searcher\Models\SearchResponse as BaseSearchResponse;
use Core\Domains\File\Collections\Files;

/**
 * @method Files getItems()
 */
class SearchResponse extends BaseSearchResponse
{

}
