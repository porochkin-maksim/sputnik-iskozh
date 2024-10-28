<?php declare(strict_types=1);

namespace Core\Services\Files\Jobs;

use Core\Queue\QueueEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class RemoveFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $filePath,
    )
    {
        $this->onQueue(QueueEnum::LOW->value);
    }

    public function handle(): void
    {
        File::delete($this->filePath);
    }
}
