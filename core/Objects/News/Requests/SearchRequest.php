<?php declare(strict_types=1);

namespace Core\Objects\News\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Objects\News\Models\NewsSearcher;

class SearchRequest extends AbstractRequest
{
    public function dto(): NewsSearcher
    {
        return new NewsSearcher();
    }
}
