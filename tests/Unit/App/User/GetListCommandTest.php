<?php declare(strict_types=1);

namespace Tests\Unit\App\User;

use Core\App\User\GetListCommand;
use Core\App\User\ListValidator;
use Core\Domains\User\UserSearchResponse;
use Core\Domains\User\UserService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class GetListCommandTest extends TestCase
{
    private UserService    $userService;
    private ListValidator  $listValidator;
    private GetListCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService   = $this->createMock(UserService::class);
        $this->listValidator = $this->createMock(ListValidator::class);
        $this->command       = new GetListCommand(
            $this->userService,
            $this->listValidator,
        );
    }

    public function test_execute_with_search(): void
    {
        $response = new UserSearchResponse;

        $this->listValidator->expects($this->once())
            ->method('validate')
            ->with(10, 0, 'id', 'asc')
        ;

        $this->userService->expects($this->once())
            ->method('search')
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, 0, 'id', 'asc', 'search', false, null, false);

        $this->assertSame($response, $result);
    }

    public function test_execute_with_membership_filter(): void
    {
        $response = new UserSearchResponse;

        $this->listValidator->method('validate');

        $this->userService->expects($this->once())
            ->method('search')
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, 0, null, null, null, false, true, true);

        $this->assertSame($response, $result);
    }

    public function test_execute_with_deleted_filter(): void
    {
        $response = new UserSearchResponse;

        $this->listValidator->method('validate');

        $this->userService->expects($this->once())
            ->method('search')
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, 0, null, null, null, true, null, false);

        $this->assertSame($response, $result);
    }

    public function test_execute_with_sorting(): void
    {
        $response = new UserSearchResponse;

        $this->listValidator->method('validate');

        $this->userService->expects($this->once())
            ->method('search')
            ->willReturn($response)
        ;

        $result = $this->command->execute(null, null, 'email', 'desc', null, false, null, false);

        $this->assertSame($response, $result);
    }

    public function test_execute_validates_before_search(): void
    {
        $this->listValidator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['limit' => ['error']]))
        ;

        $this->userService->expects($this->never())->method('search');

        $this->expectException(ValidationException::class);

        $this->command->execute(0, null, null, null, null, false, null, false);
    }
}
