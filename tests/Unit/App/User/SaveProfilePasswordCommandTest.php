<?php declare(strict_types=1);

namespace Tests\Unit\App\User;

use Core\App\User\SaveProfilePasswordCommand;
use Core\App\User\SaveProfilePasswordValidator;
use Core\Contracts\DbServiceInterface;
use Core\Domains\User\UserEntity;
use Core\Domains\User\UserService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveProfilePasswordCommandTest extends TestCase
{
    private DbServiceInterface           $dbService;
    private UserService                  $userService;
    private SaveProfilePasswordValidator $validator;
    private SaveProfilePasswordCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbService   = $this->createMock(DbServiceInterface::class);
        $this->userService = $this->createMock(UserService::class);
        $this->validator   = $this->createMock(SaveProfilePasswordValidator::class);
        $this->command     = new SaveProfilePasswordCommand(
            $this->dbService,
            $this->userService,
            $this->validator,
        );
    }

    public function test_execute_updates_password(): void
    {
        $user = new UserEntity;
        $user->setId(1);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with('NewPass1')
        ;

        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $this->userService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(UserEntity $u) => $u->getId() === 1))
        ;

        $this->command->execute($user, 'NewPass1');
    }

    public function test_execute_throws_on_invalid_password(): void
    {
        $user = new UserEntity;

        $this->validator->expects($this->once())
            ->method('validate')
            ->with('weak')
            ->willThrowException(new ValidationException(['password' => ['error']]))
        ;

        $this->expectException(ValidationException::class);
        $this->command->execute($user, 'weak');
    }
}
