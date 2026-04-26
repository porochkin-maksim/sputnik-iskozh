<?php declare(strict_types=1);

namespace Core\App\User;

use Core\Domains\User\UserEntity;
use Core\Domains\User\UserService;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class SaveProfilePasswordCommand
{
    public function __construct(
        private UserService $userService,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function execute(UserEntity $user, string $password): void
    {
        DB::transaction(function () use ($user, $password) {
            $user->setPassword($password);
            $this->userService->save($user);
        });
    }
}
