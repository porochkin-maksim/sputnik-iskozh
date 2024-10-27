<?php

namespace Core\Domains\Proposal\Jobs;

use App\Models\User;
use Core\Domains\Proposal\Notifications\ProposalNotification;
use Core\Queue\QueueEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NewProposalCreatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param string   $text
     * @param string[] $emails
     */
    public function __construct(
        private readonly string $text,
        private readonly array  $emails,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    public function handle(): void
    {
        foreach ($this->emails as $email) {
            $user = new User([User::EMAIL => $email]);
            $user->notify(new ProposalNotification($this->text));
        }
    }
}
