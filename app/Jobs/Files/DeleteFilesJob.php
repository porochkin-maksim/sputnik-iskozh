<?php declare(strict_types=1);

namespace App\Jobs\Files;

use App\Locators\FileLocator;
use Core\Domains\Files\FileTypeEnum;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use App\Services\Queue\DispatchIfNeededTrait;
use App\Services\Queue\LockableJobInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteFilesJob implements ShouldQueue, LockableJobInterface
{
    use DispatchIfNeededTrait;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int          $relatedId,
        private readonly FileTypeEnum $type,
    )
    {
    }

    public function getIdentificator(): null|int|string
    {
        return $this->type->value . '-' . $this->relatedId;
    }

    public static function getLockName(): LockNameEnum
    {
        return LockNameEnum::DELETE_FILES_JOB;
    }

    public function process(): void
    {
        FileLocator::FileService()->deleteByTypeAndRelatedId($this->type, $this->relatedId);
    }
}
