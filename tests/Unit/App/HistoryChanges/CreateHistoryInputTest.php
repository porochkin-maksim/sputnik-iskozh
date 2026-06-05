<?php declare(strict_types=1);

namespace Tests\Unit\App\HistoryChanges;

use Core\App\HistoryChanges\CreateHistoryInput;
use Core\Domains\HistoryChanges\HistoryChangesEntity;
use Tests\TestCase;

class CreateHistoryInputTest extends TestCase
{
    public function test_constructor_sets_properties(): void
    {
        $entity = new HistoryChangesEntity;
        $input  = new CreateHistoryInput($entity);

        $this->assertSame($entity, $input->historyChanges);
    }
}
