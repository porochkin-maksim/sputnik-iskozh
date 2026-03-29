<?php declare(strict_types=1);

namespace Core\Domains\User\Responses;

use Core\Db\Searcher\Models\BaseSearchResponse;
use Core\Domains\User\Collections\UserCollection;

/**
 * @method UserCollection getItems()
 */
class UserSearchResponse extends BaseSearchResponse
{

}
