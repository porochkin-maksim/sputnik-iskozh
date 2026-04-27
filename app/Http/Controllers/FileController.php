<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Billing\Payment\Models\PaymentSearcher;
use Core\Domains\Billing\Payment\PaymentLocator;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\File\Enums\FileTypeEnum;
use Core\Domains\File\FileLocator;
use Core\Domains\File\Models\FileDTO;
use Illuminate\Support\Facades\Storage;
use lc;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    public function download($filePath): BinaryFileResponse
    {
        $storagePath = $filePath;

        if ( ! Storage::exists($storagePath)) {
            if (Storage::exists('public/' . $storagePath)) {
                $fullPath = Storage::path('public/' . $filePath);
                $fileDto  = FileLocator::FileService()->getByPath('public/' . $filePath);

                $filename        = $fileDto?->getName() ? : basename($filePath);
                $encodedFilename = rawurlencode($filename); // Кодируем для UTF-8

                $headers = [
                    'Content-Type'        => Storage::mimeType($filePath),
                    'Content-Disposition' => 'inline; filename="' . $filename . '"; filename*=UTF-8\'\'' . $encodedFilename,
                ];

                return response()->file($fullPath, $headers);

            }
            abort(404, 'Файл не найден');
        }

        if ( ! lc::isAuth()) {
            abort(403);
        }

        $fileDto = FileLocator::FileService()->getByPath($filePath);
        if ( ! $fileDto) {
            abort(404, 'Файл не найден');
        }

        $this->checkAccess($fileDto);

        $fullPath = Storage::path($filePath);

        $filename        = $fileDto->getName();
        $encodedFilename = rawurlencode($filename);

        $headers = [
            'Content-Type'        => Storage::mimeType($filePath),
            'Content-Disposition' => 'inline; filename="' . $filename . '"; filename*=UTF-8\'\'' . $encodedFilename,
        ];

        return response()->file($fullPath, $headers);
    }

    private function checkAccess(FileDTO $fileDto): void
    {
        if ($fileDto->getType() === FileTypeEnum::TICKET) {
            $this->checkTicketAccess($fileDto);
        }
        elseif ($fileDto->getType() === FileTypeEnum::PAYMENT) {
            $this->checkPaymentAccess($fileDto);
        }
        elseif (in_array($fileDto->getType(), [FileTypeEnum::COUNTER_HISTORY, FileTypeEnum::COUNTER_PASSPORT], true)) {
            $this->checkCounterAccess($fileDto);
        }
        else {
            abort(403);
        }
    }

    private function checkPaymentAccess(FileDTO $fileDto): void
    {
        if (lc::roleDecorator()->can(PermissionEnum::PAYMENTS_VIEW)) {
            return;
        }

        $searcher = new PaymentSearcher();
        $searcher
            ->setId($fileDto->getRelatedId())
            ->withAccount()
            ->setLimit(1)
        ;
        $payment = PaymentLocator::PaymentService()->search($searcher)->getItems()->first();

        $account = lc::account();
        if (
            $account->getId()
            && $account->getId() === $payment->getAccount()?->getId()
        ) {
            return;
        }

        abort(403);
    }

    private function checkCounterAccess(FileDTO $fileDto): void
    {
        if (lc::roleDecorator()->canAccessAdmin()) {
            return;
        }

        $counter = null;
        if ($fileDto->getType() === FileTypeEnum::COUNTER_HISTORY) {
            $counterHistory = CounterLocator::CounterHistoryService()->getById($fileDto->getRelatedId());
            if ( ! $counterHistory) {
                abort(404);
            }
            $counter = CounterLocator::CounterService()->getById($counterHistory->getCounterId());
        }
        if ($fileDto->getType() === FileTypeEnum::COUNTER_PASSPORT) {
            $counter = CounterLocator::CounterService()->getById($fileDto->getRelatedId());
        }

        if ( ! $counter) {
            abort(404);
        }

        $account = lc::account();
        if (
            $account->getId()
            && $account->getId() === $counter?->getAccountId()
        ) {
            return;
        }

        abort(403);
    }

    private function checkTicketAccess(FileDTO $fileDto): void
    {
        if (lc::roleDecorator()->can(PermissionEnum::HELP_DESK_VIEW)) {
            return;
        }

        abort(403);
    }
}