<?php declare(strict_types=1);

namespace Core\Domains\Access\Responses;

use Core\Db\Searcher\Models\SearchResponse as BaseSearchResponse;
use Core\Domains\Access\Collections\RoleCollection;

/**
 * @method RoleCollection getItems()
 */
class SearchResponse extends BaseSearchResponse
{

}
