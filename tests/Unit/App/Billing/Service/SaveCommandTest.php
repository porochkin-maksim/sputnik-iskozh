<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Service;

use Core\App\Billing\Service\SaveCommand;
use Core\App\Billing\Service\SaveValidator;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceEntity;
use Core\Domains\Billing\Service\ServiceFactory;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveCommandTest extends TestCase
{
    private ServiceFactory        $serviceFactory;
    private ServiceCatalogService $serviceService;
    private SaveValidator         $validator;
    private SaveCommand           $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serviceFactory = $this->createMock(ServiceFactory::class);
        $this->serviceService = $this->createMock(ServiceCatalogService::class);
        $this->validator      = $this->createMock(SaveValidator::class);

        $this->command = new SaveCommand(
            $this->serviceFactory,
            $this->serviceService,
            $this->validator,
        );
    }

    public function test_execute_creates_new_service(): void
    {
        $periodId = 1;
        $type     = ServiceTypeEnum::MEMBERSHIP_FEE;
        $name     = 'Членский взнос';
        $cost     = 1000.0;
        $default  = new ServiceEntity;

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($periodId, $type, $name, $cost)
        ;

        $this->serviceFactory->expects($this->once())
            ->method('makeDefault')
            ->willReturn($default)
        ;

        $saved = (new ServiceEntity)->setId(1);

        $this->serviceService->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(ServiceEntity::class))
            ->willReturn($saved)
        ;

        $result = $this->command->execute(null, $periodId, $type, $name, $cost);

        $this->assertSame($saved, $result);
    }

    public function test_execute_updates_existing_service(): void
    {
        $id       = 1;
        $periodId = 1;
        $type     = ServiceTypeEnum::MEMBERSHIP_FEE;
        $name     = 'Членский взнос';
        $cost     = 1000.0;
        $existing = (new ServiceEntity)->setId($id);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($periodId, $type, $name, $cost)
        ;

        $this->serviceService->expects($this->once())
            ->method('getById')
            ->with($id)
            ->willReturn($existing)
        ;

        $saved = (new ServiceEntity)->setId($id);

        $this->serviceService->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(ServiceEntity::class))
            ->willReturn($saved)
        ;

        $result = $this->command->execute($id, $periodId, $type, $name, $cost);

        $this->assertSame($saved, $result);
    }

    public function test_execute_returns_null_when_not_found(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $this->serviceService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $result = $this->command->execute(999, 1, ServiceTypeEnum::OTHER, 'test', 100.0);

        $this->assertNull($result);
    }

    public function test_execute_throws_when_validator_fails(): void
    {
        $this->validator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['name' => ['error']]))
        ;

        $this->expectException(ValidationException::class);
        $this->command->execute(null, null, null, null, null);
    }
}
