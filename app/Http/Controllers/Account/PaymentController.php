<?php declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Resources\Profile\Counters\CounterListResource;
use Core\Resources\Views\ViewNames;
use Core\Responses\ResponsesEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use lc;

class PaymentController extends Controller
{

    public function __construct()
    {
    }

    public function index(): View
    {
        return view(ViewNames::PAGES_PROFILE_PAYMENTS);
    }

    public function list(): JsonResponse
    {
        return response()->json([
            ResponsesEnum::COUNTERS => new CounterListResource($result),
        ]);
    }
}
