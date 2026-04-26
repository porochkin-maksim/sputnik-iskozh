<?php declare(strict_types=1);

namespace App\Listeners;

use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function __construct(
        private readonly HistoryChangesService $historyChangesService,
    )
    {
    }

    public function handle(Login $event): void
    {
        $request = request();
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $text = sprintf(
            "Выполнен вход в систему.\nIP: %s\nUser-Agent: %s",
            $ip,
            $userAgent
        );

        $this->historyChangesService->writeToHistory(
            Event::AUTH,
            HistoryType::USER,
            $event->user->id,
            text: $text,
        );
    }
}
