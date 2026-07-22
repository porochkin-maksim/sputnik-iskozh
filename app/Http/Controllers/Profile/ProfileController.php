<?php declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Services\Users\Notificator;
use App\Session\SessionNames;
use Core\App\User\GetLoginLinkStatus\GetLoginLinkStatusQuery;
use Core\App\User\MakeLoginLink\MakeLoginLinkCommand;
use Core\App\User\MakeLoginLink\MakeLoginLinkInput;
use Core\App\User\SaveProfilePassword\SaveProfilePasswordCommand;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;
use lc;
use Throwable;

class ProfileController extends Controller
{
    public function __construct(
        private readonly SaveProfilePasswordCommand $saveProfilePasswordCommand,
        private readonly MakeLoginLinkCommand       $makeLoginLinkCommand,
        private readonly GetLoginLinkStatusQuery    $getLoginLinkStatusQuery,
        private readonly Notificator                $notificator,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function savePassword(DefaultRequest $request): void
    {
        $this->saveProfilePasswordCommand->execute(lc::user(), $request->getString('password'));
    }

    public function switchAccount(DefaultRequest $request): bool
    {
        $accountId = $request->getIntOrNull('accountId');

        $account = lc::user()->getAccounts()->searchById($accountId);

        if ($account === null) {
            return false;
        }

        Session::put(SessionNames::ACCOUNT_ID, $account->getId());

        return true;
    }

    public function getLoginLink(): JsonResponse
    {
        $result = $this->getLoginLinkStatusQuery->execute(lc::user()->getId());

        return response()->json([
            'hasLink'   => $result->hasLink,
            'qrLink'    => $result->qrLink,
            'tokenLink' => $result->tokenLink,
        ]);
    }

    public function makeLoginLink(DefaultRequest $request): JsonResponse
    {
        $user = lc::user();
        $pin  = $request->getString('pin');

        $result = $this->makeLoginLinkCommand->execute(new MakeLoginLinkInput($user->getId(), $pin));

        if ($user->isRealEmail()) {
            $this->notificator->sendLoginLinkNotification($user, $result->tokenLink, $result->pin);
        }

        return response()->json([
            'qrLink'    => $result->qrLink,
            'tokenLink' => $result->tokenLink,
            'pin'       => $result->pin,
        ]);
    }
}
