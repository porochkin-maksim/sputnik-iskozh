<?php declare(strict_types=1);

namespace Core\Domains\Poll\Models\Responses;

use Core\Db\Searcher\Models\SearchResponse as BaseSearchResponse;
use Core\Domains\Poll\Collections\PollCollection;

/**
 * @method PollCollection getItems()
 */
class PollSearchResponse extends BaseSearchResponse
{

}
