<?php declare(strict_types=1);

namespace Tests\Unit\App\CounterHistory;

use Core\App\CounterHistory\RewatchCounterHistoryChainInput;
use Tests\TestCase;

class RewatchCounterHistoryChainInputTest extends TestCase
{
    public function test_construct_sets_counter_id(): void
    {
        $input = new RewatchCounterHistoryChainInput(5);

        $this->assertSame(5, $input->counterId);
    }
}
