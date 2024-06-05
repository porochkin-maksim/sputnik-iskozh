<?php declare(strict_types=1);

namespace Core\Objects\Report\Requests;

use App\Http\Requests\AbstractRequest;
use Core\Objects\Report\Models\ReportSearcher;

class SearchRequest extends AbstractRequest
{
    public function dto(): ReportSearcher
    {
        return new ReportSearcher();
    }
}
