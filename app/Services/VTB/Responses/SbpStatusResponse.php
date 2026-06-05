<?php declare(strict_types=1);

namespace App\Services\VTB\Responses;

class SbpStatusResponse extends BaseResponse
{
    public const string QR_TYPE           = 'qrType';
    public const string QR_STATUS         = 'qrStatus';
    public const string TRANSACTION_STATE = 'transactionState';

    private readonly string $qrType;
    private readonly string $qrStatus;
    private readonly string $transactionState;

    public function __construct(array $data)
    {
        $this->qrType           = $data[self::QR_TYPE];
        $this->qrStatus         = $data[self::QR_STATUS];
        $this->transactionState = $data[self::TRANSACTION_STATE];

        parent::__construct($data);
    }

    public function getQrType(): string
    {
        return $this->qrType;
    }

    public function getQrStatus(): string
    {
        return $this->qrStatus;
    }

    public function getTransactionState(): string
    {
        return $this->transactionState;
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            [
                self::QR_TYPE           => $this->getQrType(),
                self::QR_STATUS         => $this->getQrStatus(),
                self::TRANSACTION_STATE => $this->getTransactionState(),
            ],
        );
    }
}
