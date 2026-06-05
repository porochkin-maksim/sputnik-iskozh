<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Claim;

use Core\App\Billing\Claim\CheckClaimForCounterChangeInput;
use Tests\TestCase;

class CheckClaimForCounterChangeInputTest extends TestCase
{
    public function test_construct_sets_counter_history_id(): void
    {
        $input = new CheckClaimForCounterChangeInput(42);

        $this->assertSame(42, $input->counterHistoryId);
    }
}
