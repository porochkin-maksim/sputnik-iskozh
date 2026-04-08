<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Responses;

use Core\Db\Searcher\Models\BaseSearchResponse;
use Core\Domains\HelpDesk\Collection\TicketCollection;

/**
 * @method TicketCollection getItems()
 */
class TicketSearchResponse extends BaseSearchResponse
{

}
