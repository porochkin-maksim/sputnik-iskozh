<?php declare(strict_types=1);

namespace Tests\Unit\App\User;

use Core\App\User\SetPasswordByTokenCommand;
use Core\App\User\SetPasswordByTokenValidator;
use Core\Domains\User\UserEntity;
use Core\Domains\User\UserService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SetPasswordByTokenCommandTest extends TestCase
{
    private UserService                 $userService;
    private SetPasswordByTokenValidator $validator;
    private SetPasswordByTokenCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = $this->createMock(UserService::class);
        $this->validator   = $this->createMock(SetPasswordByTokenValidator::class);
        $this->command     = new SetPasswordByTokenCommand(
            $this->userService,
            $this->validator,
        );
    }

    public function test_execute_saves_user_and_drops_token(): void
    {
        $email    = 'test@example.com';
        $password = 'NewPass1';
        $token    = 'some-token';

        $user = (new UserEntity)->setId(1);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($password, $password)
        ;

        $this->userService->expects($this->once())
            ->method('getByEmail')
            ->with($email)
            ->willReturn($user)
        ;

        $this->userService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(UserEntity $u) => $u->getId() === 1))
        ;

        $result = $this->command->execute($email, $password, $password, $token);

        $this->assertTrue($result);
    }

    public function test_execute_returns_false_when_user_not_found(): void
    {
        $this->validator->method('validate');

        $this->userService->method('getByEmail')->willReturn(null);

        $this->userService->expects($this->never())->method('save');

        $result = $this->command->execute('missing@test.com', 'Pass1', 'Pass1', 'token');

        $this->assertFalse($result);
    }

    public function test_execute_validates_before_search(): void
    {
        $this->validator->expects($this->once())
            ->method('validate')
            ->with('weak', 'weak')
            ->willThrowException(new ValidationException(['password' => ['error']]))
        ;

        $this->userService->expects($this->never())->method('getByEmail');

        $this->expectException(ValidationException::class);

        $this->command->execute('test@test.com', 'weak', 'weak', 'token');
    }
}
