<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Repositories\Shared\Cache\CacheLocator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class FeatureTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \lc::reset();
        CacheLocator::LocalCache()->dropAll();
    }
}
