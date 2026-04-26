<?php declare(strict_types=1);

namespace Core\App\User;

use Core\Domains\Infra\Tokens\TokenFacade;
use Core\Domains\User\UserService;
use Core\Exceptions\ValidationException;

readonly class SetPasswordByTokenCommand
{
    public function __construct(
        private UserService                 $userService,
        private SetPasswordByTokenValidator $validator,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(string $email, ?string $password, ?string $passwordConfirmation, string $token): bool
    {
        $this->validator->validate($password, $passwordConfirmation);

        $user = $this->userService->getByEmail($email);

        if ($user === null) {
            return false;
        }

        $user->setPassword($password);
        $this->userService->save($user);

        TokenFacade::drop($token);

        return true;
    }
}
