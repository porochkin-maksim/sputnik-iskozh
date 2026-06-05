<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Claim;

use App\Models\Account\Account;
use App\Models\Billing\Invoice;
use App\Models\Billing\Period;
use App\Models\Billing\Service;
use Core\App\Billing\Claim\SaveValidator;
use Core\Exceptions\ValidationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SaveValidatorTest extends TestCase
{
    use DatabaseTransactions;

    private SaveValidator $validator;
    private int           $periodId;
    private int           $accountId;

    protected function setUp(): void
    {
        parent::setUp();

        $period         = Period::create(['name' => 'test', 'start_at' => now(), 'end_at' => now()->addMonth()]);
        $this->periodId = $period->id;

        $account         = Account::create(['number' => 'test', 'size' => 0, 'balance' => 0, 'primary_user_id' => 1]);
        $this->accountId = $account->id;

        $this->validator = new SaveValidator;
    }

    private function createInvoice(array $data): Invoice
    {
        return Model::withoutEvents(fn() => Invoice::create($data));
    }

    private function createService(array $data): Service
    {
        return Model::withoutEvents(fn() => Service::create($data));
    }

    public function test_valid_data_passes(): void
    {
        $invoice = $this->createInvoice(['period_id' => $this->periodId, 'account_id' => $this->accountId, 'type' => 1, 'cost' => 0, 'paid' => 0]);
        $service = $this->createService(['period_id' => $this->periodId, 'type' => 1, 'name' => 'Test', 'cost' => 0]);

        $this->validator->validate($invoice->id, $service->id, 100.0, 500.0, 'test');

        $this->expectNotToPerformAssertions();
    }

    public function test_null_invoice_id_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(null, 2, 100.0, 500.0, 'test');
    }

    public function test_invoice_not_found_throws(): void
    {
        $service = $this->createService(['period_id' => $this->periodId, 'type' => 1, 'name' => 'Test', 'cost' => 0]);

        $this->expectException(ValidationException::class);
        $this->validator->validate(99999, $service->id, 100.0, 500.0, 'test');
    }

    public function test_null_service_id_throws(): void
    {
        $invoice = $this->createInvoice(['period_id' => $this->periodId, 'account_id' => $this->accountId, 'type' => 1, 'cost' => 0, 'paid' => 0]);

        $this->expectException(ValidationException::class);
        $this->validator->validate($invoice->id, null, 100.0, 500.0, 'test');
    }

    public function test_service_not_found_throws(): void
    {
        $invoice = $this->createInvoice(['period_id' => $this->periodId, 'account_id' => $this->accountId, 'type' => 1, 'cost' => 0, 'paid' => 0]);

        $this->expectException(ValidationException::class);
        $this->validator->validate($invoice->id, 99999, 100.0, 500.0, 'test');
    }

    public function test_null_tariff_throws(): void
    {
        $invoice = $this->createInvoice(['period_id' => $this->periodId, 'account_id' => $this->accountId, 'type' => 1, 'cost' => 0, 'paid' => 0]);
        $service = $this->createService(['period_id' => $this->periodId, 'type' => 1, 'name' => 'Test', 'cost' => 0]);

        $this->expectException(ValidationException::class);
        $this->validator->validate($invoice->id, $service->id, null, 500.0, 'test');
    }

    public function test_negative_tariff_throws(): void
    {
        $invoice = $this->createInvoice(['period_id' => $this->periodId, 'account_id' => $this->accountId, 'type' => 1, 'cost' => 0, 'paid' => 0]);
        $service = $this->createService(['period_id' => $this->periodId, 'type' => 1, 'name' => 'Test', 'cost' => 0]);

        $this->expectException(ValidationException::class);
        $this->validator->validate($invoice->id, $service->id, -1.0, 500.0, 'test');
    }

    public function test_null_cost_throws(): void
    {
        $invoice = $this->createInvoice(['period_id' => $this->periodId, 'account_id' => $this->accountId, 'type' => 1, 'cost' => 0, 'paid' => 0]);
        $service = $this->createService(['period_id' => $this->periodId, 'type' => 1, 'name' => 'Test', 'cost' => 0]);

        $this->expectException(ValidationException::class);
        $this->validator->validate($invoice->id, $service->id, 100.0, null, 'test');
    }

    public function test_negative_cost_throws(): void
    {
        $invoice = $this->createInvoice(['period_id' => $this->periodId, 'account_id' => $this->accountId, 'type' => 1, 'cost' => 0, 'paid' => 0]);
        $service = $this->createService(['period_id' => $this->periodId, 'type' => 1, 'name' => 'Test', 'cost' => 0]);

        $this->expectException(ValidationException::class);
        $this->validator->validate($invoice->id, $service->id, 100.0, -5.0, 'test');
    }

    public function test_name_too_long_throws(): void
    {
        $invoice = $this->createInvoice(['period_id' => $this->periodId, 'account_id' => $this->accountId, 'type' => 1, 'cost' => 0, 'paid' => 0]);
        $service = $this->createService(['period_id' => $this->periodId, 'type' => 1, 'name' => 'Test', 'cost' => 0]);

        $this->expectException(ValidationException::class);
        $this->validator->validate($invoice->id, $service->id, 100.0, 500.0, str_repeat('x', 256));
    }

    public function test_validation_returns_all_errors(): void
    {
        try {
            $this->validator->validate(null, null, null, null, str_repeat('x', 256));
            $this->fail('Expected ValidationException');
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('invoice_id', $e->errors);
            $this->assertArrayHasKey('service_id', $e->errors);
            $this->assertArrayHasKey('tariff', $e->errors);
            $this->assertArrayHasKey('cost', $e->errors);
            $this->assertArrayHasKey('name', $e->errors);
        }
    }
}
