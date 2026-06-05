<?php declare(strict_types=1);

namespace Tests\Unit\App\Counter;

use Carbon\Carbon;
use Core\App\Counter\SaveAdminCounterCommand;
use Core\App\Counter\Validator\SaveAdminCounterValidator;
use Core\Contracts\DbServiceInterface;
use Core\Domains\Counter\CounterEntity;
use Core\Domains\Counter\CounterFactory;
use Core\Domains\Counter\CounterService;
use Core\Domains\Counter\FileService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Core\Exceptions\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class SaveAdminCounterCommandTest extends TestCase
{
    private DbServiceInterface        $dbService;
    private CounterService            $counterService;
    private FileService               $fileService;
    private SaveAdminCounterValidator $validator;
    private SaveAdminCounterCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbService      = $this->createMock(DbServiceInterface::class);
        $this->counterService = $this->createMock(CounterService::class);
        $counterFactory       = new CounterFactory;
        $this->fileService    = $this->createMock(FileService::class);
        $this->validator      = $this->createMock(SaveAdminCounterValidator::class);

        $this->command = new SaveAdminCounterCommand(
            $this->dbService,
            $this->counterService,
            $counterFactory,
            $this->fileService,
            $this->validator,
        );
    }

    public function test_execute_creates_new_counter(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $this->counterService->expects($this->once())
            ->method('getById')
            ->with(0)
            ->willReturn(null)
        ;

        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $savedCounter = new CounterEntity;
        $savedCounter->setId(10);

        $this->counterService->expects($this->once())
            ->method('save')
            ->willReturn($savedCounter)
        ;

        $this->command->execute(42, 0, true, 1, '12345', null, null);
    }

    public function test_execute_updates_existing_counter(): void
    {
        $existing = new CounterEntity;
        $existing->setId(5)->setAccountId(42);

        $this->validator->expects($this->once())->method('validate');

        $this->counterService->expects($this->once())
            ->method('getById')
            ->with(5)
            ->willReturn($existing)
        ;

        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $this->counterService->expects($this->once())
            ->method('save')
            ->willReturn($existing)
        ;

        $this->command->execute(42, 5, false, 2, '67890', null, null);
    }

    public function test_execute_throws_when_account_mismatch(): void
    {
        $existing = new CounterEntity;
        $existing->setId(5)->setAccountId(100);

        $this->validator->expects($this->once())->method('validate');

        $this->counterService->expects($this->once())
            ->method('getById')
            ->with(5)
            ->willReturn($existing)
        ;

        $this->expectException(HttpException::class);
        $this->command->execute(42, 5, false, 1, '12345', null, null);
    }

    public function test_execute_with_passport_file(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $this->counterService->expects($this->once())
            ->method('getById')
            ->with(0)
            ->willReturn(null)
        ;

        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $savedCounter = new CounterEntity;
        $savedCounter->setId(10);

        $this->counterService->expects($this->once())
            ->method('save')
            ->willReturn($savedCounter)
        ;

        $this->fileService->expects($this->once())->method('storePassportFile');

        $file = new UploadedFile('passport.pdf', '/tmp/passport.pdf', 'application/pdf', 2048, 'content');
        $this->command->execute(42, 0, true, 1, '12345', Carbon::now(), $file);
    }

    public function test_execute_throws_on_validation_error(): void
    {
        $this->validator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['number' => ['error']]))
        ;

        $this->expectException(ValidationException::class);
        $this->command->execute(42, 0, true, 1, '', null, null);
    }
}
