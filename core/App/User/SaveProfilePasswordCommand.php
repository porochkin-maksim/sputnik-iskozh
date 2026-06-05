<?php declare(strict_types=1);

namespace Core\App\User;

use Core\Domains\User\UserEntity;
use Core\Domains\User\UserService;
use Core\Contracts\DbServiceInterface;
use Core\Exceptions\ValidationException;
use Throwable;

readonly class SaveProfilePasswordCommand
{
    public function __construct(
        private DbServiceInterface         $dbService,
        private UserService                $userService,
        private SaveProfilePasswordValidator $validator,
    )
    {
    }

    /**
     * @throws Throwable|ValidationException
     */
    public function execute(UserEntity $user, string $password): void
    {
        $this->validator->validate($password);

        $this->dbService->transaction(function () use ($user, $password) {
            $user->setPassword($password);
            $this->userService->save($user);
        });
    }
}
