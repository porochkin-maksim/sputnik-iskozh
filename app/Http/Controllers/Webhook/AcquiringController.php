<?php declare(strict_types=1);

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Core\App\Billing\Acquiring\HandleFailedWebhookCommand;
use Core\App\Billing\Acquiring\HandleSubmitWebhookCommand;
use Illuminate\Http\JsonResponse;

class AcquiringController extends Controller
{
    public function __construct(
        private readonly HandleSubmitWebhookCommand $submitCommand,
        private readonly HandleFailedWebhookCommand $failedCommand,
    )
    {
    }

    public function submit(int $acquiringId, string $hash): JsonResponse
    {
        return response()->json($this->submitCommand->execute($acquiringId, $hash));
    }

    public function failed(int $acquiringId, string $hash): JsonResponse
    {
        return response()->json($this->failedCommand->execute($acquiringId, $hash));
    }
}
