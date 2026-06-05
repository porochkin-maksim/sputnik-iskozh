<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Invoice;

use Core\App\Billing\Invoice\CreateRegularPeriodInvoicesInput;
use Tests\TestCase;

class CreateRegularPeriodInvoicesInputTest extends TestCase
{
    public function test_construct_with_period_id_only(): void
    {
        $input = new CreateRegularPeriodInvoicesInput(5);

        $this->assertSame(5, $input->periodId);
        $this->assertSame([], $input->accountIds);
    }

    public function test_construct_with_period_id_and_account_ids(): void
    {
        $input = new CreateRegularPeriodInvoicesInput(5, [1, 2, 3]);

        $this->assertSame(5, $input->periodId);
        $this->assertSame([1, 2, 3], $input->accountIds);
    }
}
