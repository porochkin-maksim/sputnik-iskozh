<?php declare(strict_types=1);

namespace Core\Domains\Poll\Models\Responses;

use Core\Db\Searcher\Models\SearchResponse as BaseSearchResponse;
use Core\Domains\Poll\Collections\AnswerCollection;

/**
 * @method AnswerCollection getItems()
 */
class AnswerSearchResponse extends BaseSearchResponse
{

}
