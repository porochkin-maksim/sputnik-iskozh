<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use Core\Domains\Infra\Tokens\TokenFacade;
use Core\Domains\Infra\Uid\UidFacade;
use Core\Domains\Infra\Uid\UidTypeEnum;
use App\Resources\RouteNames;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class QrCodeController extends Controller
{
    public function view(string $token)
    {
        $uid = UidFacade::find($token);

        if ( ! $uid) {
            abort(404);
        }

        return view('admin.pages.qr-view', ['uid' => $uid]);
    }

    public function makeLoginLink(int $userId, string $pin): JsonResponse
    {
        $uid = UidFacade::getUid(UidTypeEnum::LOGIN, $userId);
        TokenFacade::save(['pin' => Hash::make($pin)], $uid);

        $qrLink = route(RouteNames::ADMIN_QR_VIEW, $uid);

        $tokenLink = route(RouteNames::TOKEN, $uid);

        return response()->json([
            'qrLink'    => $qrLink,
            'tokenLink' => $tokenLink,
        ]);
    }
}
