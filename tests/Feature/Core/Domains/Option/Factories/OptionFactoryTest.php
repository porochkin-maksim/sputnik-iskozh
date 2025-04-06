<?php declare(strict_types=1);

namespace Tests\Feature\Core\Domains\Option\Factories;

use App\Models\Infra\Option;
use Carbon\Carbon;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\Factories\OptionFactory;
use Core\Domains\Option\Models\DataDTO\CounterReadingDay;
use Core\Domains\Option\Models\DataDTO\SntAccounting;
use Core\Domains\Option\Models\OptionDTO;
use Tests\TestCase;

class OptionFactoryTest extends TestCase
{
    private const TEST_DATA_SNT_ACCOUNTING = [
        'bank' => 'Test Bank',
        'acc'  => '40702810123456789012',
        'corr' => '30101810123456789012',
        'bik'  => '044123456',
        'inn'  => '1234567890',
        'kpp'  => '123456789',
        'ogrn' => '1234567890123',
    ];

    private const TEST_DATA_COUNTER_READING_DAY = [
        'day' => 25,
    ];

    private OptionFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new OptionFactory();
    }

    public function testMakeDefaultReturnsEmptyOptionDTO(): void
    {
        $result = $this->factory->makeDefault();

        $this->assertInstanceOf(OptionDTO::class, $result);
        $this->assertNull($result->getId());
        $this->assertNull($result->getType());
        $this->assertNull($result->getData());
    }

    public function testMakeWithSntAccountingTypeReturnsOptionDTOWithSntAccountingDataDTO(): void
    {
        $type = OptionEnum::SNT_ACCOUNTING;

        $result = $this->factory->makeByType($type);

        $this->assertInstanceOf(OptionDTO::class, $result);
        $this->assertEquals($type->value, $result->getId());
        $this->assertEquals($type, $result->getType());
        $this->assertInstanceOf(SntAccounting::class, $result->getData());
    }

    public function testMakeWithCounterReadingDayTypeReturnsOptionDTOWithCounterReadingDayDataDTO(): void
    {
        $type = OptionEnum::COUNTER_READING_DAY;

        $result = $this->factory->makeByType($type);

        $this->assertInstanceOf(OptionDTO::class, $result);
        $this->assertEquals($type->value, $result->getId());
        $this->assertEquals($type, $result->getType());
        $this->assertInstanceOf(CounterReadingDay::class, $result->getData());
    }

    public function testMakeModelFromDtoWithSntAccountingDataDTO(): void
    {
        $type = OptionEnum::SNT_ACCOUNTING;

        $dataDto = new SntAccounting();
        foreach (self::TEST_DATA_SNT_ACCOUNTING as $key => $value) {
            $setter = 'set' . ucfirst($key);
            $dataDto->$setter($value);
        }

        $dto = new OptionDTO();
        $dto->setId($type->value)
            ->setType($type)
            ->setData($dataDto)
        ;

        $result = $this->factory->makeModelFromDto($dto);

        $this->assertInstanceOf(Option::class, $result);
        $this->assertEquals($type->value, $result->id);
        $this->assertEquals(self::TEST_DATA_SNT_ACCOUNTING, $result->data);
    }

    public function testMakeModelFromDtoWithCounterReadingDayDataDTO(): void
    {
        $type = OptionEnum::COUNTER_READING_DAY;

        $dataDto = new CounterReadingDay();
        $dataDto->setDay(self::TEST_DATA_COUNTER_READING_DAY['day']);

        $dto = new OptionDTO();
        $dto->setId($type->value)
            ->setType($type)
            ->setData($dataDto)
        ;

        $result = $this->factory->makeModelFromDto($dto);

        $this->assertInstanceOf(Option::class, $result);
        $this->assertEquals($type->value, $result->id);
        $this->assertEquals(self::TEST_DATA_COUNTER_READING_DAY, $result->data);
    }

    public function testMakeModelFromDtoWithExistingModel(): void
    {
        $type = OptionEnum::SNT_ACCOUNTING;

        $dataDto = new SntAccounting();
        foreach (self::TEST_DATA_SNT_ACCOUNTING as $key => $value) {
            $setter = 'set' . ucfirst($key);
            $dataDto->$setter($value);
        }

        $dto = new OptionDTO();
        $dto->setId($type->value)
            ->setType($type)
            ->setData($dataDto)
        ;

        $existingModel     = new Option();
        $existingModel->id = 999; // Разные ID, чтобы проверить, что существующая модель обновляется

        $result = $this->factory->makeModelFromDto($dto, $existingModel);

        $this->assertSame($existingModel, $result);
        $this->assertEquals($type->value, $result->id);
        $this->assertEquals(self::TEST_DATA_SNT_ACCOUNTING, $result->data);
    }

    public function testMakeDtoFromObjectWithSntAccountingData(): void
    {
        $now  = Carbon::now();
        $type = OptionEnum::SNT_ACCOUNTING;

        $model             = new Option();
        $model->id         = $type->value;
        $model->data       = self::TEST_DATA_SNT_ACCOUNTING;
        $model->created_at = $now;
        $model->updated_at = $now;

        $result = $this->factory->makeDtoFromObject($model);

        $this->assertInstanceOf(OptionDTO::class, $result);
        $this->assertEquals($type->value, $result->getId());
        $this->assertEquals($type, $result->getType());
        $this->assertInstanceOf(SntAccounting::class, $result->getData());

        // Проверка свойств SntAccounting
        $dataDto = $result->getData();
        foreach (self::TEST_DATA_SNT_ACCOUNTING as $key => $value) {
            $getter = 'get' . ucfirst($key);
            $this->assertEquals($value, $dataDto->$getter());
        }

        // Используем assertEqualsWithDelta для сравнения дат, игнорируя микросекунды
        $this->assertTrue($now->toDateTimeString() === $result->getCreatedAt()->toDateTimeString(), 
            'Created dates should match without microseconds');
        $this->assertTrue($now->toDateTimeString() === $result->getUpdatedAt()->toDateTimeString(), 
            'Updated dates should match without microseconds');
    }

    public function testMakeDtoFromObjectWithCounterReadingDayData(): void
    {
        $now  = Carbon::now();
        $type = OptionEnum::COUNTER_READING_DAY;

        $model             = new Option();
        $model->id         = $type->value;
        $model->data       = self::TEST_DATA_COUNTER_READING_DAY;
        $model->created_at = $now;
        $model->updated_at = $now;

        $result = $this->factory->makeDtoFromObject($model);

        $this->assertInstanceOf(OptionDTO::class, $result);
        $this->assertEquals($type->value, $result->getId());
        $this->assertEquals($type, $result->getType());
        $this->assertInstanceOf(CounterReadingDay::class, $result->getData());

        // Проверка свойств CounterReadingDay
        $dataDto = $result->getData();
        $this->assertEquals(self::TEST_DATA_COUNTER_READING_DAY['day'], $dataDto->getDay());

        // Используем assertEqualsWithDelta для сравнения дат, игнорируя микросекунды
        $this->assertTrue($now->toDateTimeString() === $result->getCreatedAt()->toDateTimeString(), 
            'Created dates should match without microseconds');
        $this->assertTrue($now->toDateTimeString() === $result->getUpdatedAt()->toDateTimeString(), 
            'Updated dates should match without microseconds');
    }

    public function testMakeDtoFromObjectWithEmptyData(): void
    {
        $type = OptionEnum::SNT_ACCOUNTING;

        $model       = new Option();
        $model->id   = $type->value;
        $model->data = null;

        $result = $this->factory->makeDtoFromObject($model);

        $this->assertInstanceOf(OptionDTO::class, $result);
        $this->assertEquals($type->value, $result->getId());
        $this->assertEquals($type, $result->getType());
        $this->assertNull($result->getData());
    }

    public function testMakeDtoFromObjectWithUnknownType(): void
    {
        $model       = new Option();
        $model->id   = 999; // Несуществующий тип
        $model->data = ['some' => 'data'];

        $result = $this->factory->makeDtoFromObject($model);

        $this->assertInstanceOf(OptionDTO::class, $result);
        $this->assertEquals(999, $result->getId());
        $this->assertNull($result->getType());
        $this->assertNull($result->getData());
    }

    public function testCreateDataByTypeReturnsCorrectInstance(): void
    {
        $method = new \ReflectionMethod($this->factory, 'createDataByType');
        $method->setAccessible(true);

        $result1 = $method->invoke($this->factory, OptionEnum::SNT_ACCOUNTING);
        $this->assertInstanceOf(SntAccounting::class, $result1);

        $result2 = $method->invoke($this->factory, OptionEnum::COUNTER_READING_DAY);
        $this->assertInstanceOf(CounterReadingDay::class, $result2);
    }

    public function testMakeDataDtoFromArrayWithSntAccountingType(): void
    {
        $type = OptionEnum::SNT_ACCOUNTING;
        $data = [
            'bank' => 'Test Bank',
            'acc' => '40702810123456789012',
            'corr' => '30101810123456789012',
            'bik' => '044123456',
            'inn' => '1234567890',
            'kpp' => '123456789',
            'ogrn' => '1234567890123',
            'invalid_field' => 'should be ignored' // Это поле должно быть проигнорировано
        ];

        $result = $this->factory->makeDataDtoFromArray($type, $data);

        $this->assertInstanceOf(SntAccounting::class, $result);
        $this->assertEquals($data['bank'], $result->getBank());
        $this->assertEquals($data['acc'], $result->getAcc());
        $this->assertEquals($data['corr'], $result->getCorr());
        $this->assertEquals($data['bik'], $result->getBik());
        $this->assertEquals($data['inn'], $result->getInn());
        $this->assertEquals($data['kpp'], $result->getKpp());
        $this->assertEquals($data['ogrn'], $result->getOgrn());
    }

    public function testMakeDataDtoFromArrayWithCounterReadingDayType(): void
    {
        $type = OptionEnum::COUNTER_READING_DAY;
        $data = [
            'day' => 25,
            'invalid_field' => 'should be ignored' // Это поле должно быть проигнорировано
        ];

        $result = $this->factory->makeDataDtoFromArray($type, $data);

        $this->assertInstanceOf(CounterReadingDay::class, $result);
        $this->assertEquals($data['day'], $result->getDay());
    }

    public function testMakeDataDtoFromArrayWithNullType(): void
    {
        $result = $this->factory->makeDataDtoFromArray(null, ['some' => 'data']);

        $this->assertNull($result);
    }

    public function testMakeDataDtoFromArrayWithUnknownType(): void
    {
        $result = $this->factory->makeDataDtoFromArray(null, ['some' => 'data']);

        $this->assertNull($result);
    }

    public function testMakeDataDtoFromArrayWithEmptyData(): void
    {
        $type = OptionEnum::SNT_ACCOUNTING;
        $result = $this->factory->makeDataDtoFromArray($type, []);

        $this->assertInstanceOf(SntAccounting::class, $result);
        $this->assertNull($result->getBank());
        $this->assertNull($result->getAcc());
        $this->assertNull($result->getCorr());
        $this->assertNull($result->getBik());
        $this->assertNull($result->getInn());
        $this->assertNull($result->getKpp());
        $this->assertNull($result->getOgrn());
    }
}