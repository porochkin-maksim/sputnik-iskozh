<?php declare(strict_types=1);

namespace Core\Objects\File\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Objects\File\Models\FileSearcher;

class SearchRequest extends AbstractRequest
{
    public function dto(): FileSearcher
    {
        return new FileSearcher();
    }
}
