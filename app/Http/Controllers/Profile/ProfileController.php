<?php declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Session\SessionNames;
use Core\App\User\SaveProfilePasswordCommand;
use Illuminate\Support\Facades\Session;
use lc;
use Throwable;

class ProfileController extends Controller
{
    public function __construct(
        private readonly SaveProfilePasswordCommand $saveProfilePasswordCommand,
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
}
