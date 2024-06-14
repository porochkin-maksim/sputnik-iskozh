<?php declare(strict_types=1);

namespace Core\Domains\Account\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Domains\Account\Models\AccountSearcher;

class SearchRequest extends AbstractRequest
{
    public function dto(): AccountSearcher
    {
        return new AccountSearcher();
    }
}
