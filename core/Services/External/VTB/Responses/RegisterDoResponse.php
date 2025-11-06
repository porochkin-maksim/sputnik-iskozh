<?php declare(strict_types=1);

namespace Core\Services\External\VTB\Responses;

use Carbon\Carbon;

class RegisterDoResponse extends BaseResponse
{
    public const string ORDER_ID = 'orderId';
    public const string FORM_URL = 'formUrl';

    private readonly string $orderId;
    private readonly string $formUrl;

    public function __construct(array $data)
    {
        $this->orderId = $data[self::ORDER_ID];
        $this->formUrl = $data[self::FORM_URL];

        parent::__construct($data);
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getFormUrl(): string
    {
        return $this->formUrl;
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            [
                self::ORDER_ID => $this->getOrderId(),
                self::FORM_URL => $this->getFormUrl(),
            ],
        );
    }
}
