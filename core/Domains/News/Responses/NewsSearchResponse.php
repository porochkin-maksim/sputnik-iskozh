<?php declare(strict_types=1);

namespace Core\Domains\News\Responses;

use Core\Db\Searcher\Models\BaseSearchResponse;
use Core\Domains\News\Collections\NewsCollection;

/**
 * @method NewsCollection getItems()
 */
class NewsSearchResponse extends BaseSearchResponse
{

}
