<?php declare(strict_types=1);

namespace Tests\Unit\App\CounterHistory;

use Carbon\Carbon;
use Core\App\CounterHistory\AddAdminCounterHistoryCommand;
use Core\App\CounterHistory\AddAdminCounterHistoryValidator;
use Core\Contracts\DbServiceInterface;
use Core\Domains\Counter\CounterEntity;
use Core\Domains\Counter\CounterService;
use Core\Domains\Counter\FileService;
use Core\Domains\CounterHistory\CounterHistoryEntity;
use Core\Domains\CounterHistory\CounterHistoryFactory;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AddAdminCounterHistoryCommandTest extends TestCase
{
    private DbServiceInterface              $dbService;
    private CounterService                  $counterService;
    private CounterHistoryService           $counterHistoryService;
    private FileService                     $fileService;
    private AddAdminCounterHistoryValidator $validator;
    private AddAdminCounterHistoryCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbService             = $this->createMock(DbServiceInterface::class);
        $this->counterService        = $this->createMock(CounterService::class);
        $this->counterHistoryService = $this->createMock(CounterHistoryService::class);
        $counterHistoryFactory       = new CounterHistoryFactory;
        $this->fileService           = $this->createMock(FileService::class);
        $this->validator             = $this->createMock(AddAdminCounterHistoryValidator::class);

        $this->command = new AddAdminCounterHistoryCommand(
            $this->dbService,
            $this->counterService,
            $this->counterHistoryService,
            $counterHistoryFactory,
            $this->fileService,
            $this->validator,
        );
    }

    public function test_execute_creates_new_history(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $counter = new CounterEntity;
        $counter->setId(1);

        $this->counterService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($counter)
        ;

        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $savedHistory = new CounterHistoryEntity;
        $savedHistory->setId(10);

        $this->counterHistoryService->expects($this->once())
            ->method('save')
            ->willReturn($savedHistory)
        ;

        $this->command->execute(null, 1, Carbon::now(), 100, null);
    }

    public function test_execute_updates_existing_history(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $counter = new CounterEntity;
        $counter->setId(1);

        $this->counterService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($counter)
        ;

        $existingHistory = new CounterHistoryEntity;
        $existingHistory->setId(5);

        $this->counterHistoryService->expects($this->once())
            ->method('getById')
            ->with(5)
            ->willReturn($existingHistory)
        ;

        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $this->counterHistoryService->expects($this->once())
            ->method('save')
            ->willReturn($existingHistory)
        ;

        $this->command->execute(5, 1, Carbon::now(), 200, null);
    }

    public function test_execute_throws_when_counter_not_found(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $this->counterService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $this->expectException(HttpException::class);
        $this->command->execute(null, 999, Carbon::now(), 100, null);
    }

    public function test_execute_with_file(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $counter = new CounterEntity;
        $counter->setId(1);

        $this->counterService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($counter)
        ;

        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $savedHistory = new CounterHistoryEntity;
        $savedHistory->setId(10);

        $this->counterHistoryService->expects($this->once())
            ->method('save')
            ->willReturn($savedHistory)
        ;

        $this->fileService->expects($this->once())->method('deleteHistoryById');
        $this->fileService->expects($this->once())->method('storeHistoryFile');

        $file = new UploadedFile('photo.jpg', '/tmp/photo.jpg', 'image/jpeg', 1024, 'content');
        $this->command->execute(null, 1, Carbon::now(), 100, $file);
    }
}
