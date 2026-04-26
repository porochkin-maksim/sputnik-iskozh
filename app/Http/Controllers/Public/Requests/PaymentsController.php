<?php declare(strict_types=1);

namespace App\Http\Controllers\Public\Requests;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use Core\App\Billing\Payment\CreatePublicPaymentCommand;

class PaymentsController extends Controller
{
    public function __construct(
        private readonly CreatePublicPaymentCommand $command,
    )
    {
    }

    public function create(DefaultRequest $request): void
    {
        $text            = $request->getString('text');
        $account         = $request->getStringOrNull('account');
        $name            = $request->getStringOrNull('name');
        $email           = $request->getStringOrNull('email');
        $phone           = $request->getStringOrNull('phone');
        $files           = $request->allFiles();
        $attachmentLines = [];
        $i               = 1;
        foreach ($files as $file) {
            $attachmentLines[] = sprintf('%d. %s', $i++, $file->getName());
        }
        $fullText = trim(implode('', [
            $account ? sprintf("Участок: %s\n", $account) : '',
            $name ? sprintf("Обращение от: %s\n", $name) : '',
            $email ? sprintf("Почта для связи: %s\n", $email) : '',
            $phone ? sprintf("Телефон для связи: %s\n", $phone) : '',
            $attachmentLines ? sprintf("Вложения: \n%s\n", implode("\n", $attachmentLines)) : '',
            sprintf("Текст обращения:\n%s", $text),
        ]));

        $this->command->execute(
            $request->getIntOrNull('invoice'),
            $account,
            $request->getFloat('cost'),
            $text,
            $fullText,
            $files,
        );
    }
}
