<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment;

use Core\Domains\Files\FileEntity;
use Core\Domains\Files\FileSearcher;
use Core\Domains\Files\FileService;
use Core\Domains\Files\FileTypeEnum;
use Core\Domains\Files\FileServiceConfig;
use Core\Domains\Shared\ValueObjects\UploadedFile;

readonly class PaymentFileService extends FileService
{
    protected function config(): FileServiceConfig
    {
        return new FileServiceConfig(
            baseDir      : 'payment',
            baseType     : FileTypeEnum::PAYMENT,
            defaultPublic: false,
        );
    }

    /**
     * @param UploadedFile[] $files
     */
    public function storePaymentFiles(array $files, int $paymentId): void
    {
        $this->storeAndSave($files, $paymentId);
    }

    public function deletePaymentFiles(int $paymentId): void
    {
        foreach ($this->getPaymentFiles($paymentId) as $file) {
            $this->deleteById($file->getId());
        }
    }

    /**
     * @return FileEntity[]
     */
    private function getPaymentFiles(int $paymentId): array
    {
        return $this->search(
            new FileSearcher()
                ->setType(FileTypeEnum::PAYMENT)
                ->setRelatedId($paymentId),
        )->getItems();
    }
}
