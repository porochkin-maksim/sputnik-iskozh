<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment;

use Core\Domains\Files\FileService;
use Core\Domains\Files\FileTypeEnum;

readonly class PaymentFileService extends FileService
{
    protected function getBaseDir(): string
    {
        return 'payment';
    }

    protected function getBaseType(): ?FileTypeEnum
    {
        return FileTypeEnum::PAYMENT;
    }

    protected function isDefaultPublic(): ?bool
    {
        return false;
    }
}
