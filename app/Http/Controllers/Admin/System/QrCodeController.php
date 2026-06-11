<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Resources\RouteNames;
use App\Services\Users\Notificator;
use Core\Domains\Infra\Tokens\TokenFacade;
use Core\Domains\Infra\Uid\UidFacade;
use Core\Domains\Infra\Uid\UidTypeEnum;
use Core\Domains\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class QrCodeController extends Controller
{
    public function __construct(
        private readonly UserService  $userService,
        private readonly Notificator  $notificator,
    )
    {
    }

    public function view(string $token)
    {
        $uid = UidFacade::find($token);

        if ( ! $uid) {
            abort(404);
        }

        return view('pages.admin.system.qr-view', ['uid' => $uid]);
    }

    public function makeLoginLink(int $userId, string $pin): JsonResponse
    {
        $uid = UidFacade::getUid(UidTypeEnum::LOGIN, $userId);
        TokenFacade::save(['pin' => Hash::make($pin)], $uid);

        $qrLink = route(RouteNames::ADMIN_QR_VIEW, $uid);

        $tokenLink = route(RouteNames::TOKEN, $uid);

        $user = $this->userService->getById($userId);
        if ($user && $user->isRealEmail()) {
            $this->notificator->sendLoginLinkNotification($user, $tokenLink, $pin);
        }

        return response()->json([
            'qrLink'    => $qrLink,
            'tokenLink' => $tokenLink,
        ]);
    }
}
