<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Providers\VTB;

use App\Resources\RouteNames;
use App\Services\VTB\Api;
use App\Services\VTB\Exceptions\ErrorResponseException;
use App\Services\Money\MoneyService;
use Carbon\Carbon;
use Core\Domains\Billing\Acquiring\AcquiringEntity;
use Core\Domains\Billing\Acquiring\Contracts\ProviderInterface;
use Core\Domains\Billing\Acquiring\Enums\StatusEnum;
use Core\Domains\Billing\Acquiring\Exceptions\ProviderProcessException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Str;

readonly class VTBProvider implements ProviderInterface
{
    private const int MINUTES_TO_PAY = 20;

    public function __construct(
        private Api $api,
    )
    {
    }

    public function hasFullConfig(): bool
    {
        return $this->api->getConfig()->isFilled();
    }

    /**
     * @throws ProviderProcessException
     */
    public function getQrLink(AcquiringEntity $acquiring): string
    {
        $data = new VTBData($acquiring->getData());

        try {
            $registerDoResponse = $data->getRegisterDoResponse();

            if ($registerDoResponse?->getCreatedAt()->lt(Carbon::now()->subMinutes(self::MINUTES_TO_PAY))) {
                $acquiring->setStatus(StatusEnum::CANCELED);
                $registerDoResponse = null;
            }

            if ( ! $registerDoResponse) {
                $registerDoResponse = $this->api->registerDo(
                    Str::uuid()->toString(),
                    (int) MoneyService::parse($acquiring->getAmount())->getAmount(),
                    route(RouteNames::WEBHOOK_ACQURING_SUBMIT, [$acquiring->getId(), $acquiring->makeHash()]),
                    route(RouteNames::WEBHOOK_ACQURING_FAILED, [$acquiring->getId(), $acquiring->makeHash()]),
                );

                $data->setRegisterDoResponse($registerDoResponse);
            }

            $getSbpResponse = $data->getGetSbpResponse();

            if ($getSbpResponse?->getCreatedAt()->lt(Carbon::now()->subMinutes(self::MINUTES_TO_PAY))) {
                $acquiring->setStatus(StatusEnum::CANCELED);
                $getSbpResponse = null;
            }

            if ( ! $getSbpResponse) {
                $getSbpResponse = $this->api->getSbp($registerDoResponse->getOrderId());
                $data->setGetSbpResponse($getSbpResponse);
            }

            $acquiring->setData($data->jsonSerialize());

            return $getSbpResponse->getPayload();
        }
        catch (ConnectionException|ErrorResponseException $e) {
            throw new ProviderProcessException($e->getMessage());
        }
    }

    /**
     * @throws ProviderProcessException
     */
    public function getPaymentLink(AcquiringEntity $acquiring): string
    {
        $data = new VTBData($acquiring->getData());

        try {
            $registerDoResponse = $data->getRegisterDoResponse();

            if ($registerDoResponse?->getCreatedAt()->lt(Carbon::now()->subMinutes(self::MINUTES_TO_PAY))) {
                $acquiring->setStatus(StatusEnum::CANCELED);
                $registerDoResponse = null;
            }

            if ( ! $registerDoResponse) {
                $registerDoResponse = $this->api->registerDo(
                    Str::uuid()->toString(),
                    (int) MoneyService::parse($acquiring->getAmount())->getAmount(),
                    route(RouteNames::WEBHOOK_ACQURING_SUBMIT, [$acquiring->getId(), $acquiring->makeHash()]),
                    route(RouteNames::WEBHOOK_ACQURING_FAILED, [$acquiring->getId(), $acquiring->makeHash()]),
                );

                $data->setRegisterDoResponse($registerDoResponse);
            }

            $acquiring->setData($data->jsonSerialize());

            return $registerDoResponse->getFormUrl();
        }
        catch (ConnectionException|ErrorResponseException $e) {
            throw new ProviderProcessException($e->getMessage());
        }
    }
}
