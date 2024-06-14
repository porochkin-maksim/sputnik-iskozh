<?php declare(strict_types=1);

namespace Core\Domains\Report\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Domains\Report\Models\ReportSearcher;

class SearchRequest extends AbstractRequest
{
    public function dto(): ReportSearcher
    {
        return new ReportSearcher();
    }
}
