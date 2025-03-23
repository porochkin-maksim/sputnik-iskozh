<?php

namespace App\Http\Controllers;

use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Billing\Payment\Models\PaymentSearcher;
use Core\Domains\Billing\Payment\PaymentLocator;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\File\Enums\TypeEnum;
use Core\Domains\File\FileLocator;
use Core\Domains\File\Models\FileDTO;
use Illuminate\Support\Facades\Storage;
use lc;

class FileController extends Controller
{
    public function download($filePath)
    {
        if ( ! lc::isAuth()) {
            abort(403);
        }
        $storagePath = $filePath;

        if ( ! Storage::exists($storagePath)) {
            abort(404, 'Файл не найден');
        }

        $fileDto = FileLocator::FileService()->getByPath($filePath);
        if ( ! $fileDto) {
            abort(404, 'Файл не найден');
        }

        $this->checkAccess($fileDto);

        $fullPath = Storage::path($filePath);

        $headers = [
            'Content-Type'        => Storage::mimeType($filePath),
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"', // Заменили attachment на inline
        ];

        return response()->file($fullPath, $headers);
    }

    private function checkAccess(FileDTO $fileDto): void
    {
        if ($fileDto->getType() === TypeEnum::PAYMENT) {
            $this->checkPaymentAccess($fileDto);
        }
        elseif ($fileDto->getType() === TypeEnum::COUNTER) {
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

        $counterHistory = CounterLocator::CounterHistoryService()->getById($fileDto->getRelatedId());
        if ( ! $counterHistory) {
            abort(404);
        }

        $counter = CounterLocator::CounterService()->getById($counterHistory->getCounterId());

        $account = lc::account();
        if (
            $account->getId()
            && $account->getId() === $counter?->getAccountId()
        ) {
            return;
        }

        abort(403);
    }
}