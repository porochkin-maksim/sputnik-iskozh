<?php declare(strict_types=1);

namespace App\Listeners;

use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
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

        HistoryChangesLocator::HistoryChangesService()->writeToHistory(
            Event::COMMON,
            HistoryType::USER,
            $event->user->id,
            text: $text,
        );
    }
}