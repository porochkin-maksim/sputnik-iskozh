<?php declare(strict_types=1);

namespace App\Jobs\Proposal;

use App\Services\Files\Collections\TmpFiles;
use App\Services\Queue\QueueEnum;
use Core\Domains\Proposal\Mails\NewProposalMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Attachment;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProposalNotifyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string   $proposalFilePath,
        private readonly string   $fromName,
        private readonly string   $email,
        private readonly TmpFiles $tmpFiles,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    public function handle(): void
    {
        $mail = new NewProposalMail(
            $this->email,
            $this->fromName,
            $this->makeAttachments(),
        );
        Mail::send($mail);
    }

    /**
     * @return Attachment[]
     */
    private function makeAttachments(): array
    {
        $result   = [];
        $result[] = Attachment::fromPath($this->proposalFilePath)->as('Предложение.pdf');

        foreach ($this->tmpFiles as $tmpFile) {
            $result[] = Attachment::fromPath($tmpFile->getPath())->as($tmpFile->getOriginalName());
        }

        return $result;
    }
}
