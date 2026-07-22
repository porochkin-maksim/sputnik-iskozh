<?php declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\DefaultRequest;
use App\Models\User;
use App\Resources\RouteNames;
use Carbon\Carbon;
use Core\App\User\SetPasswordByToken\SetPasswordByTokenCommand;
use Core\Domains\Infra\Tokens\TokenRepositoryInterface;
use Core\Domains\User\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class SetPasswordController extends AbstractAuthController
{
    public function __construct(
        private readonly UserService               $userService,
        private readonly SetPasswordByTokenCommand $setPasswordByTokenCommand,
        private readonly TokenRepositoryInterface  $tokenRepository,
    )
    {
    }

    public function index(DefaultRequest $request)
    {
        try {
            $token = $request->getStringOrNull('token');
            $email = $this->getEmail($request);

            if ( ! $email) {
                return redirect()->route(RouteNames::INDEX);
            }

            return view('pages.auth.passwords.set', compact('email', 'token'));

        }
        catch (Exception) {
            return redirect()->route(RouteNames::INDEX);
        }
    }

    public function set(DefaultRequest $request): JsonResponse
    {
        $email = $this->getEmail($request);

        if ( ! $email) {
            return $this->sendResetFailedResponse($request, Password::INVALID_TOKEN);
        }

        $user = $this->userService->getByEmail($email);

        if ( ! $user) {
            return $this->sendResetFailedResponse($request, Password::INVALID_TOKEN);
        }

        $result = $this->setPasswordByTokenCommand->execute(
            $email,
            $request->getStringOrNull('password'),
            $request->getStringOrNull('password_confirmation'),
            $request->getString('token'),
        );

        if ( ! $result) {
            return $this->sendResetFailedResponse($request, Password::INVALID_TOKEN);
        }

        Auth::loginUsingId($user->getId());

        $authenticatedUser = Auth::user();
        $authenticatedUser?->forceFill([User::LOGGED_IN_AT => Carbon::now()])->save();

        return response()->json(['redirect' => route(RouteNames::HOME)]);
    }

    private function sendResetFailedResponse(DefaultRequest $request, string $response): JsonResponse
    {
        return response()->json(['message' => __($response)], 422);
    }

    private function getEmail(DefaultRequest $request): ?string
    {
        $token = $request->getStringOrNull('token');

        if ($token === null) {
            return null;
        }

        $data = $this->tokenRepository->find($token);

        if (empty($data)) {
            return null;
        }

        $email   = $data['email'];
        $expires = Carbon::parse($data['expires']);
        if (now()->gt($expires)) {
            return null;
        }

        return $email;
    }
}
