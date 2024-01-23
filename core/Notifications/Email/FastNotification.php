<?php

namespace Core\Notifications\Email;

use Core\Queue\QueueEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class FastNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected ?string $title        = null;
    protected ?string $text         = null;
    protected ?string $buttonLabel  = null;
    protected ?string $link         = null;
    protected array   $optionalText = [];
    protected array   $attach       = [];

    public function __construct()
    {
        $this->onQueue(QueueEnum::EMAIL->value);
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function setText(string $text): static
    {
        $this->text = $text;
        return $this;
    }

    public function setButtonLabel(string $buttonLabel): static
    {
        $this->buttonLabel = $buttonLabel;
        return $this;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;
        return $this;
    }

    public function setOptionalText(array $optionalText): static
    {
        $this->optionalText = $optionalText;
        return $this;
    }

    public function setAttach(array $attach): static
    {
        $this->attach = $attach;
        return $this;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $result = (new MailMessage)->subject($this->title)
                                   ->line($this->text)
        ;

        if ($this->buttonLabel && $this->link) {
            $result = $result->action($this->buttonLabel, $this->link);
        }

        foreach ($this->optionalText as $ot) {
            $result = $result->line($ot);
        }
        foreach ($this->attach as $attach) {
            $result = $result->attach($attach);
        }

        return $result;
    }
}
