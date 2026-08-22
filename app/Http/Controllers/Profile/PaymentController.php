<?php declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use Core\App\Billing\Payment\CreatePublicPaymentCommand;
use Illuminate\Http\JsonResponse;
use lc;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(
        private CreatePublicPaymentCommand $command,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function send(DefaultRequest $request): JsonResponse
    {
        $account = lc::account();
        $text    = $request->getStringOrNull('name') ?? 'Пополнение счёта';

        $this->command->execute(
            invoiceId: null,
            accountNumber: $account?->getNumber(),
            cost: $request->getFloat('cost'),
            text: $text,
            fullText: $text,
            files: $request->files('files', []),
        );

        return response()->json(['success' => true, 'message' => 'Платёж отправлен на проверку']);
    }
}
