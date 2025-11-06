<?php declare(strict_types=1);

/**
 * @see https://sandbox.vtb.ru/integration/api/rest.html
 * @see https://sandbox.vtb.ru/integration/api/sbp.html#sbp-api
 */
namespace Core\Services\External\VTB;

use Core\Services\External\VTB\Exceptions\ErrorResponseException;
use Core\Services\External\VTB\Factories\ResponseFactory;
use Core\Services\External\VTB\Responses\GetSbpResponse;
use Core\Services\External\VTB\Responses\RegisterDoResponse;
use Core\Services\External\VTB\Responses\SbpStatusResponse;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

readonly class Api
{
    public function __construct(
        private ApiConfig       $config,
        private ResponseFactory $factory,
    )
    {
    }

    public function getConfig(): ApiConfig
    {
        return clone $this->config;
    }

    /**
     * @return array<string, string>
     */
    private function getConfigForRequest(): array
    {
        if ($this->config->getToken()) {
            return [
                'token' => $this->config->getToken(),
            ];
        }

        if ($this->config->getUserName() && $this->config->getPassword()) {
            return [
                'userName' => $this->config->getUserName(),
                'password' => $this->config->getPassword(),
            ];
        }

        throw new \RuntimeException('Конфигурация подключения не заполнена');
    }

    /**
     * @param string      $orderNumber Локальный номер заказа
     * @param int         $amount      Сумма платежа в копейках
     * @param string      $returnUrl   Полный адрес, на который требуется перенаправить пользователя в случае успешной оплаты
     * @param string|null $failUrl     Полный адрес, на который требуется перенаправить пользователя в случае неуспешной оплаты
     *
     * @return RegisterDoResponse
     *
     * @throws ConnectionException
     * @throws ErrorResponseException
     */
    public function registerDo(
        string  $orderNumber,
        int     $amount,
        string  $returnUrl,
        ?string $failUrl = null,
    ): RegisterDoResponse
    {
        $payload = array_merge([
            'amount'      => $amount,
            'orderNumber' => $orderNumber,
            'returnUrl'   => $returnUrl,
            'failUrl'     => $failUrl,
        ],
            $this->getConfigForRequest(),
        );

        $result = Http::asForm()->post($this->config->getUrl() . '/register.do', $payload);

        return $this->factory->makeRegisterDoResponse($result->getBody()->getContents());
    }

    /**
     * @param string $mdOrder Номер заказа в платежном шлюзе. Уникален в пределах платежного шлюза. @see RegisterDoResponse::$orderId
     *
     * @return GetSbpResponse
     *
     * @throws ConnectionException
     * @throws ErrorResponseException
     */
    public function getSbp(
        string $mdOrder,
    ): GetSbpResponse
    {
        $payload = array_merge([
            'mdOrder'  => $mdOrder,
            'qrFormat' => 'image',
        ],
            $this->getConfigForRequest(),
        );

        $result = Http::asForm()->post($this->config->getUrl() . '/sbp/c2b/qr/dynamic/get.do', $payload);

        return $this->factory->makeGetSbpResponse($result->getBody()->getContents());
    }

    /**
     * @param string      $mdOrder Номер заказа в платежном шлюзе. Уникален в пределах платежного шлюза. @see RegisterDoResponse::$orderId
     * @param string|null $qrId    Идентификатор QR-кода. @see GetSbpResponse::$qrId
     *
     * @return SbpStatusResponse
     *
     * @throws ConnectionException
     * @throws ErrorResponseException
     */
    public function getSbpStatus(
        string  $mdOrder,
        ?string $qrId = null,
    ): SbpStatusResponse
    {
        $payload = array_merge([
            'mdOrder' => $mdOrder,
            'qrId'    => $qrId,
        ],
            $this->getConfigForRequest(),
        );

        $result = Http::asForm()->post($this->config->getUrl() . '/sbp/c2b/qr/status.do', $payload);

        return $this->factory->makeSbpStatusResponse($result->getBody()->getContents());
    }
}
