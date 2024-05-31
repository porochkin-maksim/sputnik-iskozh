<?php declare(strict_types=1);

namespace Core\Objects\Report\Requests;

use App\Http\Requests\AbstractRequest;
use Carbon\Carbon;
use Core\Enums\DateTimeFormat;
use Core\Objects\Report\Enums\CategoryEnum;
use Core\Objects\Report\Enums\TypeEnum;
use Core\Objects\Report\Models\ReportDTO;
use Core\Objects\Report\Models\ReportSearcher;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Validation\Rule;

class SearchRequest extends AbstractRequest
{
    public function dto(): ReportSearcher
    {
        $dto = new ReportSearcher();

        return $dto;
    }
}
