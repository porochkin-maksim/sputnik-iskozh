<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\HelpDesk;

use App\Http\Controllers\Controller;
use App\Http\Resources\Common\SelectResource;
use Core\Domains\HelpDesk\Enums\TicketTypeEnum;
use Illuminate\Http\JsonResponse;

class TicketTypeController extends Controller
{
    public function list(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'types'   => new SelectResource(TicketTypeEnum::array()),
        ]);
    }
}
