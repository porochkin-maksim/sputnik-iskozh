<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Payment;

use Core\App\Billing\Payment\SaveImportPaymentsInput;
use Core\Domains\Billing\Events\ImportPaymentData;
use Tests\TestCase;

class SaveImportPaymentsInputTest extends TestCase
{
    public function test_construct_sets_payments_data(): void
    {
        $data  = [new ImportPaymentData(1, 100.0)];
        $input = new SaveImportPaymentsInput($data);

        $this->assertSame($data, $input->paymentsData);
    }

    public function test_construct_with_empty_array(): void
    {
        $input = new SaveImportPaymentsInput([]);

        $this->assertSame([], $input->paymentsData);
    }
}
