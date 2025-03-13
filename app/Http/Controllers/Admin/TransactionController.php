<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use lc;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Transactions\SaveRequest;
use App\Http\Resources\Admin\Transactions\ServicesListResource;
use App\Http\Resources\Admin\Transactions\TransactionResource;
use App\Http\Resources\Admin\Transactions\TransactionsListResource;
use App\Http\Resources\Common\SelectResource;
use App\Models\Billing\Service;
use App\Models\Billing\Transaction;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Services\InvoiceService;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Service\Models\ServiceDTO;
use Core\Domains\Billing\Service\Models\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceLocator;
use Core\Domains\Billing\Service\Services\ServiceService;
use Core\Domains\Billing\Transaction\Factories\TransactionFactory;
use Core\Domains\Billing\Transaction\Models\TransactionDTO;
use Core\Domains\Billing\Transaction\Models\TransactionSearcher;
use Core\Domains\Billing\Transaction\Responses\SearchResponse;
use Core\Domains\Billing\Transaction\Services\TransactionService;
use Core\Domains\Billing\Transaction\TransactionLocator;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    private TransactionFactory $transactionFactory;
    private TransactionService $transactionService;
    private InvoiceService     $invoiceService;
    private ServiceService     $serviceService;

    public function __construct()
    {
        $this->transactionService = TransactionLocator::TransactionService();
        $this->transactionFactory = TransactionLocator::TransactionFactory();
        $this->invoiceService     = InvoiceLocator::InvoiceService();
        $this->serviceService     = ServiceLocator::ServiceService();
    }

    public function create(int $invoiceId): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::TRANSACTIONS_EDIT)) {
            abort(403);
        }

        $invoice = $this->invoiceService->getById($invoiceId);
        if ( ! $invoice) {
            abort(412);
        }

        $transactions       = $this->getInvoiceTransactions($invoice->getId())->getItems();
        $existingServiceIds = array_map(static fn(TransactionDTO $transaction) => $transaction->getServiceId(), $transactions->toArray());

        $services = $this->serviceService->search(
            (new ServiceSearcher())
                ->setPeriodId($invoice->getPeriodId())
                ->setSortOrderProperty(Service::TYPE, SearcherInterface::SORT_ORDER_ASC)
        )->getItems();

        if ($invoice->getType() === InvoiceTypeEnum::REGULAR) {
            $services = $services->filter(static function (ServiceDTO $service) use ($existingServiceIds) {
                return (
                        ! in_array($service->getId(), $existingServiceIds, true)
                        && $service->getType() !== ServiceTypeEnum::ELECTRIC_TARIFF
                    )
                    || $service->getType() === ServiceTypeEnum::OTHER;
            });
        }
        else {
            $services = $services->filter(static function (ServiceDTO $service) {
                return in_array($service->getType(), [
                    ServiceTypeEnum::ELECTRIC_TARIFF,
                    ServiceTypeEnum::OTHER,
                ], true);
            });
        }

        $servicesMap = [];
        foreach ($services as $service) {
            $servicesMap[$service->getId()] = $service->getName();
        }
        $transaction = $this->transactionFactory->makeDefault()->setServiceId($services->first()?->getId());

        return response()->json([
            'servicesSelect' => new SelectResource($servicesMap),
            'transaction'    => new TransactionResource($transaction),
            'services'       => new ServicesListResource($services),
        ]);
    }

    public function get(int $invoiceId, int $transactionId): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::TRANSACTIONS_VIEW)) {
            abort(403);
        }
        if ( ! $invoiceId || ! $transactionId) {
            abort(412);
        }

        $transaction        = $this->transactionService->getById($transactionId);
        $invoice            = $this->invoiceService->getById($invoiceId);
        $transactionService = $this->serviceService->getById($transaction?->getServiceId());
        if ( ! $transaction || ! $invoice || ! $transactionService) {
            abort(412);
        }

        $transactions       = $this->getInvoiceTransactions($invoice->getId())->getItems();
        $existingServiceIds = array_map(static fn(TransactionDTO $transaction) => $transaction->getServiceId(), $transactions->toArray());

        $services = $this->serviceService->search(
            (new ServiceSearcher())
                ->setPeriodId($invoice->getPeriodId())
                ->setSortOrderProperty(Service::TYPE, SearcherInterface::SORT_ORDER_ASC)
        )->getItems();

        if ($invoice->getType() === InvoiceTypeEnum::REGULAR) {
            $services = $services->filter(static function (ServiceDTO $service) use ($existingServiceIds) {
                return (
                        ! in_array($service->getId(), $existingServiceIds, true)
                        && $service->getType() !== ServiceTypeEnum::ELECTRIC_TARIFF
                    )
                    || $service->getType() === ServiceTypeEnum::OTHER;
            });
        }
        else {
            $services = $services->filter(static function (ServiceDTO $service) {
                return in_array($service->getType(), [
                    ServiceTypeEnum::ELECTRIC_TARIFF,
                    ServiceTypeEnum::OTHER,
                ], true);
            });
        }

        $servicesMap = [];
        foreach ($services as $service) {
            $servicesMap[$service->getId()] = $service->getName();
        }
        $servicesMap[$transactionService->getId()] = $transactionService->getName();

        $transaction = $transaction->setService($transactionService);

        return response()->json([
            'servicesSelect' => new SelectResource($servicesMap),
            'transaction'    => new TransactionResource($transaction),
            'services'       => new ServicesListResource($services),
        ]);
    }

    public function save(SaveRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::TRANSACTIONS_EDIT)) {
            abort(403);
        }

        $transaction = $request->getId()
            ? $this->transactionService->getById($request->getId())
            : $this->transactionFactory->makeDefault()
                ->setInvoiceId($request->getInvoiceId())
                ->setServiceId($request->getServiceId());

        if ( ! $transaction) {
            abort(404);
        }

        $transaction
            ->setName($request->getName())
            ->setTariff($request->getTariff() ? : $request->getCost())
            ->setCost($request->getCost());

        $transaction = $this->transactionService->save($transaction);

        return response()->json([
            'transaction' => new TransactionResource($transaction),
        ]);
    }

    public function list(int $invoiceId): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::TRANSACTIONS_VIEW)) {
            abort(403);
        }

        $transactions = $this->getInvoiceTransactions($invoiceId);

        return response()->json(new TransactionsListResource(
            $transactions->getItems(),
        ));
    }

    public function delete(int $invoiceId, int $id): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::TRANSACTIONS_DROP)) {
            abort(403);
        }

        return $this->transactionService->deleteById($id);
    }

    private function getInvoiceTransactions(int $invoiceId): SearchResponse
    {
        $searcher = new TransactionSearcher();
        $searcher
            ->setInvoiceId($invoiceId)
            ->setWithService()
            ->setSortOrderProperty(Transaction::SERVICE_ID, SearcherInterface::SORT_ORDER_ASC);

        return $this->transactionService->search($searcher);
    }
}
