<?php declare(strict_types=1);

namespace Tests\Feature\Core\Domains\Option\Repositories;

use App\Models\Infra\Option;
use Core\Db\Searcher\Collections\WhereCollection;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Option\Repositories\OptionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ReflectionClass;

class OptionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private const TEST_ID   = 1;
    private const TEST_DATA = [
        'accounting' => [
            'inn' => '1234567890',
            'kpp' => '123456789',
        ],
    ];

    private OptionRepository $repository;
    private ReflectionClass  $reflection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new OptionRepository();
        $this->reflection = new ReflectionClass($this->repository);
    }

    public function testModelClassReturnsCorrectClass(): void
    {
        $method = $this->reflection->getMethod('modelClass');
        $method->setAccessible(true);
        $result = $method->invoke($this->repository);

        $this->assertEquals(Option::class, $result);
    }

    public function testGetByIdReturnsNullWhenIdIsNull(): void
    {
        $result = $this->repository->getById(null);

        $this->assertNull($result);
    }

    public function testGetByIdReturnsOptionWhenFound(): void
    {
        $option       = new Option();
        $option->id   = self::TEST_ID;
        $option->data = self::TEST_DATA;
        $option->save();

        $result = $this->repository->getById(self::TEST_ID);

        $this->assertInstanceOf(Option::class, $result);
        $this->assertEquals(self::TEST_ID, $result->id);
        $this->assertEquals(self::TEST_DATA, $result->data);
    }

    public function testGetByIdsReturnsArrayOfOptions(): void
    {
        $ids     = [1, 2, 3];
        $options = [];

        foreach ($ids as $id) {
            $option       = new Option();
            $option->id   = $id;
            $option->data = self::TEST_DATA;
            $option->save();
            $options[] = $option;
        }

        $result = $this->repository->getByIds($ids);

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        foreach ($result as $option) {
            $this->assertInstanceOf(Option::class, $option);
            $this->assertContains($option->id, $ids);
            $this->assertEquals(self::TEST_DATA, $option->data);
        }
    }

    public function testGetByIdsWithSearcherReturnsFilteredOptions(): void
    {
        $this->setUp();
        $ids = [1, 2, 3];
        foreach ($ids as $id) {
            $option = new Option();
            $option->id = $id;
            $option->data = self::TEST_DATA;
            $option->save();
            $this->assertDatabaseHas('options', ['id' => $id]);
        }
        $whereCollection = $this->createMock(WhereCollection::class);
        $whereCollection->method('isEmpty')->willReturn(false);
        $whereCollection->method('toArray')->willReturn([
            ['column' => 'id', 'operator' => 'IN', 'value' => $ids],
        ]);

        $searcher = $this->createMock(SearcherInterface::class);
        $searcher->method('getIds')->willReturn($ids);
        $searcher->method('getWhere')->willReturn($whereCollection);
        $searcher->method('getOrWhere')->willReturn($this->createMock(WhereCollection::class));
        $searcher->method('getSortProperties')->willReturn([]);
        $searcher->method('getLimit')->willReturn(null);
        $searcher->method('getOffset')->willReturn(null);
        $searcher->method('getLastId')->willReturn(null);
        $searcher->method('getWith')->willReturn([]);
        $searcher->method('getSelect')->willReturn([]);
        $searcher->method('getWhereColumn')->willReturn($this->createMock(WhereCollection::class));
        $searcher->expects($this->exactly(2))->method('getWhere')->willReturn($whereCollection);

        $options = $this->repository->getByIds($ids, $searcher);

        $this->assertIsArray($options);
        $this->assertCount(3, $options);
        foreach ($options as $option) {
            $this->assertInstanceOf(Option::class, $option);
            $this->assertContains($option->id, $ids);
            $this->assertEquals(self::TEST_DATA, $option->data);
        }
    }

    public function testSavePersistsOption(): void
    {
        $option       = new Option();
        $option->data = self::TEST_DATA;

        $result = $this->repository->save($option);

        $this->assertInstanceOf(Option::class, $result);
        $this->assertNotNull($result->id);
        $this->assertEquals(self::TEST_DATA, $result->data);
    }

    public function testSaveUpdatesExistingOption(): void
    {
        $option       = new Option();
        $option->data = self::TEST_DATA;
        $option->save();

        $updatedData  = [
            'accounting' => [
                'inn' => '0987654321',
                'kpp' => '987654321',
            ],
        ];
        $option->data = $updatedData;

        $result = $this->repository->save($option);

        $this->assertInstanceOf(Option::class, $result);
        $this->assertEquals($option->id, $result->id);
        $this->assertEquals($updatedData, $result->data);
    }
} 