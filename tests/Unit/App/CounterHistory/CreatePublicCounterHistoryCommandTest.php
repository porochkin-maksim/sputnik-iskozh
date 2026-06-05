<?php declare(strict_types=1);

namespace Tests\Unit\App\CounterHistory;

use Core\App\CounterHistory\CreatePublicCounterHistoryCommand;
use Core\App\CounterHistory\CreatePublicCounterHistoryValidator;
use Core\Contracts\DbServiceInterface;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountService;
use Core\Domains\Counter\CounterCollection;
use Core\Domains\Counter\CounterEntity;
use Core\Domains\Counter\CounterService;
use Core\Domains\Counter\FileService;
use Core\Domains\CounterHistory\CounterHistoryEntity;
use Core\Domains\CounterHistory\CounterHistoryFactory;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\HistoryChanges\HistoryChangesEntity;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class CreatePublicCounterHistoryCommandTest extends TestCase
{
    private DbServiceInterface                  $dbService;
    private CounterService                      $counterService;
    private CounterHistoryService               $counterHistoryService;
    private CounterHistoryFactory               $counterHistoryFactory;
    private FileService                         $fileService;
    private HistoryChangesService               $historyChangesService;
    private AccountService                      $accountService;
    private CreatePublicCounterHistoryValidator $validator;
    private CreatePublicCounterHistoryCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbService             = $this->createMock(DbServiceInterface::class);
        $this->counterService        = $this->createMock(CounterService::class);
        $this->counterHistoryService = $this->createMock(CounterHistoryService::class);
        $this->counterHistoryFactory = new CounterHistoryFactory;
        $this->fileService           = $this->createMock(FileService::class);
        $this->historyChangesService = $this->createMock(HistoryChangesService::class);
        $this->accountService        = $this->createMock(AccountService::class);
        $this->validator             = $this->createMock(CreatePublicCounterHistoryValidator::class);

        $this->command = new CreatePublicCounterHistoryCommand(
            $this->dbService,
            $this->counterService,
            $this->counterHistoryService,
            $this->counterHistoryFactory,
            $this->fileService,
            $this->historyChangesService,
            $this->accountService,
            $this->validator,
        );
    }

    public function test_execute_validates_before_any_action(): void
    {
        $this->validator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['accountNumber' => ['error']]))
        ;

        $this->expectException(ValidationException::class);

        $this->command->execute('', null, null, 100, null, 'text');
    }

    public function test_execute_throws_when_no_file(): void
    {
        $this->validator->method('validate');

        $this->expectException(ValidationException::class);

        $this->command->execute('A-001', null, '123', 100, null, 'text');
    }

    public function test_execute_happy_path_with_account(): void
    {
        $this->validator->method('validate');

        $this->dbService->method('transaction')
            ->willReturnCallback(fn(callable $cb) => $cb())
        ;

        $account = (new AccountEntity)->setId(5);
        $this->accountService->method('findByNumber')->with('A-001')->willReturn($account);

        $counter  = (new CounterEntity)->setId(10)->setIsInvoicing(true);
        $counters = new CounterCollection([$counter]);
        $this->counterService->method('getByAccountId')->with(5)->willReturn($counters);

        $this->counterHistoryService->method('save')
            ->willReturnCallback(fn(CounterHistoryEntity $e) => $e->setId(20))
        ;

        $file = new UploadedFile('reading.jpg', '/tmp/reading.jpg', 'image/jpeg', 1024, 'content');
        $this->fileService->expects($this->once())
            ->method('storeHistoryFile')
            ->with($file, 20)
        ;

        $historyEntity = new HistoryChangesEntity;
        $this->historyChangesService->method('makeHistory')->willReturn($historyEntity);

        $this->historyChangesService->expects($this->once())
            ->method('save')
            ->with($historyEntity)
        ;

        $this->command->execute('A-001', null, '123', 100, $file, 'full text');
    }

    public function test_execute_happy_path_without_account(): void
    {
        $this->validator->method('validate');

        $this->dbService->method('transaction')
            ->willReturnCallback(fn(callable $cb) => $cb())
        ;

        $this->accountService->method('findByNumber')->willReturn(null);

        $this->counterService->expects($this->never())->method('getByAccountId');

        $this->counterHistoryService->method('save')
            ->willReturnCallback(fn(CounterHistoryEntity $e) => $e->setId(21))
        ;

        $file = new UploadedFile('reading.jpg', '/tmp/reading.jpg', 'image/jpeg', 1024, 'content');
        $this->fileService->expects($this->once())
            ->method('storeHistoryFile')
            ->with($file, 21)
        ;

        $historyEntity = new HistoryChangesEntity;
        $this->historyChangesService->method('makeHistory')->willReturn($historyEntity);

        $this->historyChangesService->expects($this->once())->method('save');

        $this->command->execute('', null, null, 100, $file, 'full text');
    }

    public function test_execute_with_counter_id(): void
    {
        $this->validator->method('validate');

        $this->dbService->method('transaction')
            ->willReturnCallback(fn(callable $cb) => $cb())
        ;

        $account = (new AccountEntity)->setId(5);
        $this->accountService->method('findByNumber')->willReturn($account);

        $counter  = (new CounterEntity)->setId(10);
        $counter2 = (new CounterEntity)->setId(15)->setIsInvoicing(true);
        $counters = new CounterCollection([$counter, $counter2]);
        $this->counterService->method('getByAccountId')->willReturn($counters);

        $this->counterHistoryService->method('save')
            ->willReturnCallback(fn(CounterHistoryEntity $e) => $e->setId(22))
        ;

        $file = new UploadedFile('reading.jpg', '/tmp/reading.jpg', 'image/jpeg', 1024, 'content');

        $historyEntity = new HistoryChangesEntity;
        $this->historyChangesService->method('makeHistory')->willReturn($historyEntity);
        $this->historyChangesService->method('save');

        $this->fileService->expects($this->once())->method('storeHistoryFile');

        $this->command->execute('A-001', 10, null, 100, $file, 'text');
    }
}
