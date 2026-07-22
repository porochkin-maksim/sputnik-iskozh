<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Services\Users\Notificator;
use Core\App\User\MakeLoginLink\MakeLoginLinkCommand;
use Core\App\User\MakeLoginLink\MakeLoginLinkInput;
use Core\Domains\Infra\Uid\UidFacade;
use Core\Domains\User\UserService;
use Illuminate\Http\JsonResponse;

class QrCodeController extends Controller
{
    public function __construct(
        private readonly MakeLoginLinkCommand $makeLoginLinkCommand,
        private readonly UserService          $userService,
        private readonly Notificator          $notificator,
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
        $result = $this->makeLoginLinkCommand->execute(new MakeLoginLinkInput($userId, $pin));

        $user = $this->userService->getById($userId);
        if ($user && $user->isRealEmail()) {
            $this->notificator->sendLoginLinkNotification($user, $result->tokenLink, $result->pin);
        }

        return response()->json([
            'qrLink'    => $result->qrLink,
            'tokenLink' => $result->tokenLink,
        ]);
    }

    public function makeLoginLinkAndSendEmail(int $userId): JsonResponse
    {
        $result = $this->makeLoginLinkCommand->execute(new MakeLoginLinkInput($userId));

        $user = $this->userService->getById($userId);
        if ($user && $user->isRealEmail()) {
            $this->notificator->sendLoginLinkNotification($user, $result->tokenLink, $result->pin);
        }

        return response()->json(['sent' => true]);
    }
}
