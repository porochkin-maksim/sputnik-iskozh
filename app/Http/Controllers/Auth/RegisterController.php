<?php

namespace App\Http\Controllers\Auth;

use Core\Objects\ObjectsLocator;
use Illuminate\Support\Facades\DB;
use Core\Auth\Requests\RegisterRequest;
use Core\Objects\User\Services\UserService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends AbstractAuthController
{
    use RegistersUsers;

    private readonly UserService $userService;

    public function __construct()
    {
        $this->userService = ObjectsLocator::Users()->UserService();

        $this->middleware('guest');
    }

    public function __invoke(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = $this->userService->save($request->dto());

            $this->guard()->login($user->getModel());

            if ($response = $this->registered($request, $user)) {
                return $response;
            }

            $message = <<<HTML
Регистрация успешна!
<br>
Проверьте почту <b>{$user->getEmail()}</b>
HTML;

            event(new Registered($user->getModel()));
            
            DB::commit();

            return $request->wantsJson()
                ? new JsonResponse($message, 200)
                : redirect($this->redirectPath());
        }
        catch (\Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
    }
}
