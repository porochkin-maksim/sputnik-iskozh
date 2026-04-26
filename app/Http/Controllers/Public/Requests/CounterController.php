<?php declare(strict_types=1);

namespace App\Http\Controllers\Public\Requests;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use Core\App\CounterHistory\CreatePublicCounterHistoryCommand;
use Core\Exceptions\ValidationException;
use Throwable;

class CounterController extends Controller
{
    public function __construct(
        private readonly CreatePublicCounterHistoryCommand $command,
    )
    {
    }

    /**
     * @throws Throwable
     * @throws ValidationException
     */
    public function create(DefaultRequest $request): void
    {
        $account         = $request->getString('account');
        $counter         = $request->getStringOrNull('counter');
        $name            = $request->getStringOrNull('name');
        $email           = $request->getStringOrNull('email');
        $phone           = $request->getStringOrNull('phone');
        $value           = $request->getInt('value');
        $files           = $request->allFiles();
        $attachmentLines = [];
        $i               = 1;
        foreach ($files as $file) {
            $attachmentLines[] = sprintf('%d. %s', $i++, $file->getName());
        }
        $fullText = sprintf(
            "%s%s%s%s%s%s%s",
            $account ? sprintf("Участок: %s\n", $account) : '',
            $counter ? sprintf("Счётчик №: %s\n", $counter) : '',
            $value ? sprintf("Показание: %s\n", $value) : '',
            $name ? sprintf("Обращение от: %s\n", $name) : '',
            $email ? sprintf("Почта для связи: %s\n", $email) : '',
            $phone ? sprintf("Телефон для связи: %s\n", $phone) : '',
            $attachmentLines ? sprintf("Вложения: \n%s\n", implode("\n", $attachmentLines)) : '',
        );

        $this->command->execute(
            $account,
            $request->getIntOrNull('counter_id'),
            $counter,
            $value,
            $request->file('file'),
            $fullText,
        );
    }
}
