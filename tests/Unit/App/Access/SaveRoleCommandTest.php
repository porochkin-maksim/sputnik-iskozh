<?php declare(strict_types=1);

namespace Tests\Unit\App\Access;

use Core\App\Access\SaveRoleCommand;
use Core\App\Access\SaveRoleValidator;
use Core\Domains\Access\RoleEntity;
use Core\Domains\Access\RoleFactory;
use Core\Domains\Access\RoleService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveRoleCommandTest extends TestCase
{
    private RoleFactory       $roleFactory;
    private RoleService       $roleService;
    private SaveRoleValidator $saveRoleValidator;
    private SaveRoleCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->roleFactory       = $this->createMock(RoleFactory::class);
        $this->roleService       = $this->createMock(RoleService::class);
        $this->saveRoleValidator = $this->createMock(SaveRoleValidator::class);
        $this->command           = new SaveRoleCommand(
            $this->roleFactory,
            $this->roleService,
            $this->saveRoleValidator,
        );
    }

    public function test_execute_creates_new_role(): void
    {
        $default = new RoleEntity;
        $saved   = (new RoleEntity)->setId(1);

        $this->saveRoleValidator->method('validate');

        $this->roleFactory->method('makeDefault')->willReturn($default);

        $this->roleService->expects($this->once())
            ->method('save')
            ->willReturn($saved)
        ;

        $result = $this->command->execute(null, 'Moderator', [1, 2]);

        $this->assertSame($saved, $result);
    }

    public function test_execute_updates_existing_role(): void
    {
        $existing = (new RoleEntity)->setId(5);
        $saved    = (new RoleEntity)->setId(5);

        $this->saveRoleValidator->method('validate');

        $this->roleService->method('getById')->with(5)->willReturn($existing);

        $this->roleService->expects($this->once())
            ->method('save')
            ->willReturn($saved)
        ;

        $result = $this->command->execute(5, 'Updated', [3]);

        $this->assertSame($saved, $result);
    }

    public function test_execute_returns_null_when_role_not_found(): void
    {
        $this->saveRoleValidator->method('validate');

        $this->roleService->method('getById')->willReturn(null);

        $this->roleService->expects($this->never())->method('save');

        $result = $this->command->execute(999, 'Ghost', [1]);

        $this->assertNull($result);
    }

    public function test_execute_validates_before_any_action(): void
    {
        $this->saveRoleValidator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['name' => ['error']]))
        ;

        $this->roleService->expects($this->never())->method('getById');

        $this->expectException(ValidationException::class);

        $this->command->execute(null, '', []);
    }
}
