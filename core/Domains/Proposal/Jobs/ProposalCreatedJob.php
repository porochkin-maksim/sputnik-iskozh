<?php

namespace Core\Domains\Proposal\Jobs;

use Core\Queue\QueueEnum;
use Core\Services\Files\Collections\TmpFiles;
use Core\Services\Files\Jobs\RemoveFileJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class ProposalCreatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param string[] $emails
     */
    public function __construct(
        private readonly string   $proposalFilePath,
        private readonly string   $fromName,
        private readonly array    $emails,
        private readonly TmpFiles $tmpFiles,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    public function handle(): void
    {
        $jobs = [];
        foreach ($this->emails as $email) {
            $jobs[] = new ProposalNotifyJob(
                $this->proposalFilePath,
                $this->fromName,
                $email,
                $this->tmpFiles,
            );
        }

        foreach ($this->tmpFiles as $tmpFile) {
            $jobs[] = new RemoveFileJob($tmpFile->getPath());
        }
        $jobs[] = new RemoveFileJob($this->proposalFilePath);

        Bus::chain($jobs)->dispatch();
    }
}
