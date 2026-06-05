<?php declare(strict_types=1);

namespace Tests\Unit\App\CounterHistory;

use Carbon\Carbon;
use Core\App\CounterHistory\AutoIncrementingCounterHistoriesCommand;
use Core\Domains\Counter\CounterCollection;
use Core\Domains\Counter\CounterEntity;
use Core\Domains\Counter\CounterSearchResponse;
use Core\Domains\Counter\CounterService;
use Core\Domains\CounterHistory\CounterHistoryEntity;
use Core\Domains\CounterHistory\CounterHistoryFactory;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\Models\DataDTO\CounterReadingDay;
use Core\Domains\Option\OptionEntity;
use Core\Domains\Option\OptionService;
use Tests\TestCase;

class AutoIncrementingCounterHistoriesCommandTest extends TestCase
{
    private CounterService                          $counterService;
    private CounterHistoryService                   $counterHistoryService;
    private CounterHistoryFactory                   $counterHistoryFactory;
    private HistoryChangesService                   $historyChangesService;
    private OptionService                           $optionService;
    private AutoIncrementingCounterHistoriesCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->counterService        = $this->createMock(CounterService::class);
        $this->counterHistoryService = $this->createMock(CounterHistoryService::class);
        $this->counterHistoryFactory = new CounterHistoryFactory;
        $this->historyChangesService = $this->createMock(HistoryChangesService::class);
        $this->optionService         = $this->createMock(OptionService::class);

        $this->command = new AutoIncrementingCounterHistoriesCommand(
            $this->counterService,
            $this->counterHistoryService,
            $this->counterHistoryFactory,
            $this->historyChangesService,
            $this->optionService,
        );
    }

    public function test_execute_returns_early_when_no_reading_day(): void
    {
        $option = new OptionEntity;
        $option->setData(new CounterReadingDay);

        $this->optionService->method('getByType')->with(OptionEnum::COUNTER_READING_DAY)->willReturn($option);

        $this->counterService->expects($this->never())->method('search');

        $this->command->execute();
    }

    public function test_execute_returns_early_when_day_does_not_match(): void
    {
        $data = new CounterReadingDay;
        $data->setDay(Carbon::now()->subDay()->day);

        $option = new OptionEntity;
        $option->setData($data);

        $this->optionService->method('getByType')->willReturn($option);

        $this->counterService->expects($this->never())->method('search');

        $this->command->execute();
    }

    public function test_execute_returns_early_when_no_counters(): void
    {
        $data = new CounterReadingDay;
        $data->setDay(Carbon::now()->day);

        $option = new OptionEntity;
        $option->setData($data);

        $this->optionService->method('getByType')->willReturn($option);

        $response = new CounterSearchResponse;
        $response->setItems(new CounterCollection);
        $this->counterService->method('search')->willReturn($response);

        $this->counterHistoryService->expects($this->never())->method('save');

        $this->command->execute();
    }

    public function test_execute_skips_snt_counter(): void
    {
        $data = new CounterReadingDay;
        $data->setDay(Carbon::now()->day);

        $option = new OptionEntity;
        $option->setData($data);

        $this->optionService->method('getByType')->willReturn($option);

        $counter  = (new CounterEntity)->setId(1)->setAccountId(1)->setIncrement(10);
        $response = new CounterSearchResponse;
        $response->setItems(new CounterCollection([$counter]));
        $this->counterService->method('search')->willReturn($response);

        $this->counterHistoryService->expects($this->never())->method('save');

        $this->command->execute();
    }

    public function test_execute_skips_counter_with_current_month_history(): void
    {
        $data = new CounterReadingDay;
        $data->setDay(Carbon::now()->day);

        $option = new OptionEntity;
        $option->setData($data);

        $this->optionService->method('getByType')->willReturn($option);

        $counter = (new CounterEntity)->setId(1)->setAccountId(5)->setIncrement(10);

        $response = new CounterSearchResponse;
        $response->setItems(new CounterCollection([$counter]));
        $this->counterService->method('search')->willReturn($response);

        $lastHistory = (new CounterHistoryEntity)->setDate(Carbon::now());
        $this->counterHistoryService->method('getLastByCounterId')->willReturn($lastHistory);

        $this->counterHistoryService->expects($this->never())->method('save');

        $this->command->execute();
    }

    public function test_execute_creates_history_for_counter(): void
    {
        $data = new CounterReadingDay;
        $data->setDay(Carbon::now()->day);

        $option = new OptionEntity;
        $option->setData($data);

        $this->optionService->method('getByType')->willReturn($option);

        $counter = (new CounterEntity)->setId(1)->setAccountId(5)->setIncrement(10);

        $response = new CounterSearchResponse;
        $response->setItems(new CounterCollection([$counter]));
        $this->counterService->method('search')->willReturn($response);

        $lastHistory = (new CounterHistoryEntity)
            ->setId(3)
            ->setValue(100)
            ->setIsVerified(true)
            ->setDate(Carbon::now()->subMonthNoOverflow())
        ;

        $this->counterHistoryService->method('getLastByCounterId')
            ->willReturn($lastHistory)
        ;

        $this->counterHistoryService->expects($this->once())
            ->method('save')
            ->willReturnCallback(fn(CounterHistoryEntity $h) => $h->setId(4))
        ;

        $this->historyChangesService->expects($this->once())
            ->method('writeToHistory')
        ;

        $this->command->execute();
    }

    public function test_execute_handles_no_last_history(): void
    {
        $data = new CounterReadingDay;
        $data->setDay(Carbon::now()->day);

        $option = new OptionEntity;
        $option->setData($data);

        $this->optionService->method('getByType')->willReturn($option);

        $counter = (new CounterEntity)->setId(1)->setAccountId(5)->setIncrement(10);

        $response = new CounterSearchResponse;
        $response->setItems(new CounterCollection([$counter]));
        $this->counterService->method('search')->willReturn($response);

        $this->counterHistoryService->method('getLastByCounterId')->willReturn(null);

        $this->counterHistoryService->expects($this->once())
            ->method('save')
            ->willReturnCallback(fn(CounterHistoryEntity $h) => $h->setId(5))
        ;

        $this->historyChangesService->expects($this->once())
            ->method('writeToHistory')
        ;

        $this->command->execute();
    }
}
