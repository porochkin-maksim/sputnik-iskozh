<?php declare(strict_types=1);

namespace Core\Objects\Account\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Objects\Account\Models\AccountSearcher;

class SearchRequest extends AbstractRequest
{
    public function dto(): AccountSearcher
    {
        return new AccountSearcher();
    }
}
