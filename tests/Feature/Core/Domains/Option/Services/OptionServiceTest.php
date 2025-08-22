<?php declare(strict_types=1);

namespace Tests\Feature\Core\Domains\Option\Services;

use App\Models\Infra\Option;
use Core\Domains\Infra\Comparator\DTO\AbstractComparatorDTO;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\Factories\ComparatorFactory;
use Core\Domains\Option\Factories\OptionFactory;
use Core\Domains\Option\Models\DataDTO\CounterReadingDay;
use Core\Domains\Option\Models\DataDTO\SntAccounting;
use Core\Domains\Option\Models\OptionDTO;
use Core\Domains\Option\Models\OptionSearcher;
use Core\Domains\Option\Repositories\OptionRepository;
use Core\Domains\Option\Responses\SearchResponse;
use Core\Domains\Option\Services\OptionService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class OptionServiceTest extends TestCase
{
    private const TEST_ID   = 1;
    private const TEST_DATA = [
        'accounting' => [
            'inn' => '1234567890',
            'kpp' => '123456789',
        ],
    ];

    private OptionService         $service;
    private OptionFactory         $factory;
    private OptionRepository      $repository;
    private HistoryChangesService $historyService;
    private ComparatorFactory     $comparatorFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory           = $this->createMock(OptionFactory::class);
        $this->repository        = $this->createMock(OptionRepository::class);
        $this->historyService    = $this->createMock(HistoryChangesService::class);
        $this->comparatorFactory = $this->createMock(ComparatorFactory::class);

        $this->service = new OptionService(
            $this->factory,
            $this->repository,
            $this->historyService,
            $this->comparatorFactory,
        );
    }

    public function testGetByIdReturnsNullWhenIdIsNull(): void
    {
        $result = $this->service->getById(null);

        $this->assertNull($result);
    }

    public function testGetByIdReturnsNullWhenIdIsNotValidOptionEnum(): void
    {
        $invalidId = 999; // Несуществующий OptionEnum

        $searcher = new OptionSearcher();
        $searcher->setId($invalidId);

        $searchResponse = new SearchResponse();
        $searchResponse->setItems(new Collection());

        $this->repository
            ->expects($this->once())
            ->method('search')
            ->with($searcher)
            ->willReturn($searchResponse)
        ;

        $result = $this->service->getById($invalidId);

        $this->assertNull($result);
    }

    public function testGetByIdReturnsNewDtoWhenOptionNotFoundButValidEnum(): void
    {
        $type = OptionEnum::SNT_ACCOUNTING;
        $id   = $type->value;

        $searcher = new OptionSearcher();
        $searcher->setId($id);

        $searchResponse = new SearchResponse();
        $searchResponse->setItems(new Collection());

        $newDto = new OptionDTO();
        $newDto->setId($id);
        $newDto->setType($type);

        $this->repository
            ->expects($this->once())
            ->method('search')
            ->with($searcher)
            ->willReturn($searchResponse)
        ;

        $this->factory
            ->expects($this->once())
            ->method('makeByType')
            ->with($type)
            ->willReturn($newDto)
        ;

        $result = $this->service->getById($id);

        $this->assertInstanceOf(OptionDTO::class, $result);
        $this->assertEquals($id, $result->getId());
        $this->assertEquals($type, $result->getType());
    }

    public function testGetByIdReturnsOptionDTOWhenFound(): void
    {
        $searcher = new OptionSearcher();
        $searcher->setId(self::TEST_ID);

        $option       = new Option();
        $option->id   = self::TEST_ID;
        $option->data = self::TEST_DATA;

        $dataDto = new SntAccounting();
        $dataDto->setInn('1234567890');
        $dataDto->setKpp('123456789');

        $dto = new OptionDTO();
        $dto->setId(self::TEST_ID);
        $dto->setData($dataDto);
        $dto->setType(OptionEnum::SNT_ACCOUNTING);

        $searchResponse = new SearchResponse();
        $searchResponse->setItems(new Collection([$option]));

        $this->repository
            ->expects($this->once())
            ->method('search')
            ->with($searcher)
            ->willReturn($searchResponse)
        ;

        $this->factory
            ->expects($this->once())
            ->method('makeDtoFromObject')
            ->with($option)
            ->willReturn($dto)
        ;

        $result = $this->service->getById(self::TEST_ID);

        $this->assertInstanceOf(OptionDTO::class, $result);
        $this->assertEquals(self::TEST_ID, $result->getId());
        $this->assertInstanceOf(SntAccounting::class, $result->getData());
        $this->assertEquals('1234567890', $result->getData()->getInn());
        $this->assertEquals('123456789', $result->getData()->getKpp());
    }

    public function testGetByTypeReturnsExistingOption(): void
    {
        $type = OptionEnum::SNT_ACCOUNTING;
        $id   = $type->value;

        $option       = new Option();
        $option->id   = $id;
        $option->data = self::TEST_DATA;

        $dataDto = new SntAccounting();
        $dataDto->setInn('1234567890');
        $dataDto->setKpp('123456789');

        $dto = new OptionDTO();
        $dto->setId($id);
        $dto->setType($type);
        $dto->setData($dataDto);

        $searcher = new OptionSearcher();
        $searcher->setId($id);

        $searchResponse = new SearchResponse();
        $searchResponse->setItems(new Collection([$option]));

        $this->repository
            ->expects($this->once())
            ->method('search')
            ->with($searcher)
            ->willReturn($searchResponse)
        ;

        $this->factory
            ->expects($this->once())
            ->method('makeDtoFromObject')
            ->with($option)
            ->willReturn($dto)
        ;

        $result = $this->service->getByType($type);

        $this->assertInstanceOf(OptionDTO::class, $result);
        $this->assertEquals($id, $result->getId());
        $this->assertEquals($type, $result->getType());
        $this->assertInstanceOf(SntAccounting::class, $result->getData());
        $this->assertEquals('1234567890', $result->getData()->getInn());
        $this->assertEquals('123456789', $result->getData()->getKpp());
    }

    public function testGetByTypeCreatesNewOptionWhenNotFound(): void
    {
        $type = OptionEnum::SNT_ACCOUNTING;
        $id   = $type->value;

        $searcher = new OptionSearcher();
        $searcher->setId($id);

        $searchResponse = new SearchResponse();
        $searchResponse->setItems(new Collection());

        $dto = new OptionDTO();
        $dto->setId($id);
        $dto->setType($type);

        $this->repository
            ->expects($this->once())
            ->method('search')
            ->with($searcher)
            ->willReturn($searchResponse)
        ;

        $this->factory
            ->expects($this->once())
            ->method('makeByType')
            ->with($type)
            ->willReturn($dto)
        ;

        $result = $this->service->getByType($type);

        $this->assertInstanceOf(OptionDTO::class, $result);
        $this->assertEquals($id, $result->getId());
        $this->assertEquals($type, $result->getType());
    }

    public function testSaveCreatesNewOption(): void
    {
        $dataDto = new SntAccounting();
        $dataDto->setInn('1234567890');
        $dataDto->setKpp('123456789');

        $dto = new OptionDTO();
        $dto->setId(null);
        $dto->setType(OptionEnum::SNT_ACCOUNTING);
        $dto->setData($dataDto);

        $model       = new Option();
        $model->data = self::TEST_DATA;

        $emptyDto = new OptionDTO();

        $mockComparatorBefore = $this->createMock(AbstractComparatorDTO::class);
        $mockComparatorAfter  = $this->createMock(AbstractComparatorDTO::class);

        $this->repository
            ->expects($this->once())
            ->method('getById')
            ->with(null)
            ->willReturn(null)
        ;

        $this->factory
            ->expects($this->once())
            ->method('makeModelFromDto')
            ->with($dto, null)
            ->willReturn($model)
        ;

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($model)
            ->willReturn($model)
        ;

        $this->factory
            ->expects($this->once())
            ->method('makeDtoFromObject')
            ->with($model)
            ->willReturn($dto)
        ;

        // Настраиваем mock для createComparator
        $this->comparatorFactory->method('createComparator')
            ->willReturnCallback(function ($arg) use ($mockComparatorAfter, $mockComparatorBefore) {
                if ($arg instanceof OptionDTO) {
                    // Если DTO с данными, то возвращаем mockComparatorAfter
                    if ($arg->getData() instanceof SntAccounting) {
                        return $mockComparatorAfter;
                    }
                    // Если пустой DTO, то возвращаем mockComparatorBefore
                    if ($arg->getData() === null && $arg->getId() === null) {
                        return $mockComparatorBefore;
                    }
                }

                return $this->createMock(AbstractComparatorDTO::class);
            })
        ;

        // Проверяем, что writeToHistory вызывается с правильными аргументами
        $this->historyService
            ->expects($this->once())
            ->method('writeToHistory')
            ->with(
                Event::CREATE,
                HistoryType::OPTION,
                $dto->getId(),
                null,
                null,
                $mockComparatorAfter,
                $mockComparatorBefore,
            )
        ;

        $result = $this->service->save($dto);

        $this->assertInstanceOf(OptionDTO::class, $result);
        $this->assertInstanceOf(SntAccounting::class, $result->getData());
        $this->assertEquals('1234567890', $result->getData()->getInn());
        $this->assertEquals('123456789', $result->getData()->getKpp());
    }

    public function testSaveUpdatesExistingOption(): void
    {
        $dataDto = new SntAccounting();
        $dataDto->setInn('1234567890');
        $dataDto->setKpp('123456789');

        $dto = new OptionDTO();
        $dto->setId(self::TEST_ID);
        $dto->setType(OptionEnum::SNT_ACCOUNTING);
        $dto->setData($dataDto);

        $model       = new Option();
        $model->id   = self::TEST_ID;
        $model->data = self::TEST_DATA;

        $oldDto = new OptionDTO();
        $oldDto->setId(self::TEST_ID);
        $oldDto->setType(OptionEnum::SNT_ACCOUNTING);

        $mockComparatorBefore = $this->createMock(AbstractComparatorDTO::class);
        $mockComparatorAfter  = $this->createMock(AbstractComparatorDTO::class);

        $this->repository
            ->expects($this->once())
            ->method('getById')
            ->with(self::TEST_ID)
            ->willReturn($model)
        ;

        $this->factory
            ->expects($this->exactly(2))
            ->method('makeDtoFromObject')
            ->with($model)
            ->willReturn($oldDto, $dto)
        ;

        $this->factory
            ->expects($this->once())
            ->method('makeModelFromDto')
            ->with($dto, $model)
            ->willReturn($model)
        ;

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($model)
            ->willReturn($model)
        ;

        // Настраиваем mock для createComparator
        $this->comparatorFactory->method('createComparator')
            ->willReturnCallback(function ($arg) use ($mockComparatorAfter, $mockComparatorBefore, $dto, $oldDto) {
                if ($arg instanceof OptionDTO) {
                    // Проверяем ID и тип объекта
                    if ($arg->getId() === self::TEST_ID) {
                        // Если DTO с данными, то возвращаем mockComparatorAfter
                        if ($arg->getData() instanceof SntAccounting) {
                            return $mockComparatorAfter;
                        }
                        // Если DTO без данных, то возвращаем mockComparatorBefore
                        if ($arg->getData() === null) {
                            return $mockComparatorBefore;
                        }
                    }
                }

                return $this->createMock(AbstractComparatorDTO::class);
            })
        ;

        // Проверяем, что writeToHistory вызывается с правильными аргументами
        $this->historyService
            ->expects($this->once())
            ->method('writeToHistory')
            ->with(
                Event::UPDATE,
                HistoryType::OPTION,
                $dto->getId(),
                null,
                null,
                $mockComparatorAfter,
                $mockComparatorBefore,
            )
        ;

        $result = $this->service->save($dto);

        $this->assertInstanceOf(OptionDTO::class, $result);
        $this->assertEquals(self::TEST_ID, $result->getId());
        $this->assertInstanceOf(SntAccounting::class, $result->getData());
        $this->assertEquals('1234567890', $result->getData()->getInn());
        $this->assertEquals('123456789', $result->getData()->getKpp());
    }

    public function testAllReturnsAllEnumOptionsWithExistingAndNewOnes(): void
    {
        // Создаем существующую опцию в БД
        $existingOption = new Option();
        $existingOption->id = OptionEnum::SNT_ACCOUNTING->value;
        $existingOption->data = ['test' => 'data'];

        // Настраиваем response от репозитория
        $searchResponse = new SearchResponse();
        $searchResponse->setItems(new Collection([$existingOption]));

        // Настраиваем ожидаемый вызов search
        $this->repository
            ->expects($this->once())
            ->method('search')
            ->with($this->callback(function ($actualSearcher) {
                return $actualSearcher instanceof OptionSearcher;
            }))
            ->willReturn($searchResponse);

        // Настраиваем фабрику для создания DTO из существующей опции
        $existingDto = new OptionDTO();
        $existingDto->setId(OptionEnum::SNT_ACCOUNTING->value);
        $existingDto->setType(OptionEnum::SNT_ACCOUNTING);
        $existingDto->setData(new SntAccounting());

        $this->factory
            ->expects($this->once())
            ->method('makeDtoFromObject')
            ->with($existingOption)
            ->willReturn($existingDto);

        // Настраиваем фабрику для создания новых DTO для остальных типов
        $counterReadingDto = new OptionDTO();
        $counterReadingDto->setId(OptionEnum::COUNTER_READING_DAY->value);
        $counterReadingDto->setType(OptionEnum::COUNTER_READING_DAY);
        $counterReadingDto->setData(new CounterReadingDay());

        $this->factory
            ->expects($this->exactly(count(OptionEnum::cases()) - 1))
            ->method('makeByType')
            ->willReturnCallback(function (OptionEnum $type) use ($counterReadingDto) {
                if ($type === OptionEnum::COUNTER_READING_DAY) {
                    return $counterReadingDto;
                }
                $dto = new OptionDTO();
                $dto->setId($type->value);
                $dto->setType($type);
                return $dto;
            });

        // Вызываем тестируемый метод
        $result = $this->service->all();

        // Проверяем результат
        $this->assertInstanceOf(SearchResponse::class, $result);
        $items = $result->getItems();
        
        // Проверяем, что количество элементов равно количеству значений в enum
        $this->assertEquals(count(OptionEnum::cases()), count($items));
        $this->assertEquals(count(OptionEnum::cases()), $result->getTotal());

        // Проверяем, что все значения из enum присутствуют
        $foundIds = [];
        foreach ($items as $item) {
            $foundIds[] = $item->getId();
            $this->assertInstanceOf(OptionDTO::class, $item);
            
            if ($item->getId() === OptionEnum::SNT_ACCOUNTING->value) {
                // Проверяем существующую опцию
                $this->assertSame($existingDto, $item);
            } elseif ($item->getId() === OptionEnum::COUNTER_READING_DAY->value) {
                // Проверяем новую опцию с CounterReadingDay
                $this->assertSame($counterReadingDto, $item);
            }
        }

        // Проверяем, что все ID из enum присутствуют в результате
        foreach (OptionEnum::cases() as $case) {
            $this->assertContains($case->value, $foundIds);
        }
    }
} 