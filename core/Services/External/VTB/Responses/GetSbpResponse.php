<?php declare(strict_types=1);

namespace Core\Services\External\VTB\Responses;

class GetSbpResponse extends BaseResponse
{
    public const string QR_ID     = 'qrId';
    public const string PAYLOAD   = 'payload';
    public const string QR_STATUS = 'qrStatus';

    private readonly string $qrId;
    private readonly string $payload;
    private readonly string $qrStatus;

    public function __construct(array $data)
    {
        $this->qrId     = $data[self::QR_ID];
        $this->payload  = $data[self::PAYLOAD];
        $this->qrStatus = $data[self::QR_STATUS];

        parent::__construct($data);
    }

    public function getQrId(): string
    {
        return $this->qrId;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    public function getQrStatus(): string
    {
        return $this->qrStatus;
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            [
                self::QR_ID     => $this->getQrId(),
                self::PAYLOAD   => $this->getPayload(),
                self::QR_STATUS => $this->getQrStatus(),
            ],
        );
    }
}
