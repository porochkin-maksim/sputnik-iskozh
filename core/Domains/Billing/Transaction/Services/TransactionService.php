<?php declare(strict_types=1);

namespace Core\Domains\Billing\Transaction\Services;

use Core\Domains\Billing\Transaction\Collections\TransactionCollection;
use Core\Domains\Billing\Transaction\Events\TransactionDeletedEvent;
use Core\Domains\Billing\Transaction\Events\TransactionsUpdatedEvent;
use Core\Domains\Billing\Transaction\Factories\TransactionFactory;
use Core\Domains\Billing\Transaction\Models\TransactionComparator;
use Core\Domains\Billing\Transaction\Models\TransactionDTO;
use Core\Domains\Billing\Transaction\Models\TransactionSearcher;
use Core\Domains\Billing\Transaction\Repositories\TransactionRepository;
use Core\Domains\Billing\Transaction\Responses\SearchResponse;
use Core\Domains\Billing\TransactionToObject\TransactionToObjectLocator;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;
use Exception;
use Illuminate\Support\Facades\DB;

readonly class TransactionService
{
    public function __construct(
        private TransactionFactory    $transactionFactory,
        private TransactionRepository $transactionRepository,
        private HistoryChangesService $historyChangesService,
    )
    {
    }

    public function save(TransactionDTO $transaction): TransactionDTO
    {
        $model = $this->transactionRepository->getById($transaction->getId());
        if ($model) {
            $before = $this->transactionFactory->makeDtoFromObject($model);
        }
        else {
            $before = new TransactionDTO();
        }

        $model   = $this->transactionRepository->save($this->transactionFactory->makeModelFromDto($transaction, $model));
        $current = $this->transactionFactory->makeDtoFromObject($model);

        $this->historyChangesService->writeToHistory(
            $transaction->getId() ? Event::UPDATE : Event::CREATE,
            HistoryType::INVOICE,
            $current->getInvoiceId(),
            HistoryType::TRANSACTION,
            $current->getId(),
            new TransactionComparator($current),
            new TransactionComparator($before),
        );

        if (
            ! $transaction->getId()
            || $current->getCost() !== $before->getCost()
            || $current->getPayed() !== $before->getPayed()
        ) {
            TransactionsUpdatedEvent::dispatch($current->getInvoiceId());
        }

        return $current;
    }

    /**
     * @param TransactionCollection $transactions
     */
    public function saveCollection(TransactionCollection $transactions): TransactionCollection
    {
        $result = new TransactionCollection();
        foreach ($transactions as $transaction) {
            $result->add($this->save($transaction));
        }

        return $result;
    }

    public function search(TransactionSearcher $searcher): SearchResponse
    {
        $response = $this->transactionRepository->search($searcher);

        $result = new SearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new TransactionCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->transactionFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
    }

    public function getById(?int $id): ?TransactionDTO
    {
        if ( ! $id) {
            return null;
        }

        $searcher = new TransactionSearcher();
        $searcher->setId($id);
        $result = $this->transactionRepository->search($searcher)->getItems()->first();

        return $result ? $this->transactionFactory->makeDtoFromObject($result) : null;
    }

    public function deleteById(int $id): bool
    {
        DB::beginTransaction();
        try {
            $transaction = $this->getById($id);

            if ( ! $transaction) {
                return false;
            }

            $this->historyChangesService->writeToHistory(
                Event::DELETE,
                HistoryType::INVOICE,
                $transaction->getInvoiceId(),
                HistoryType::TRANSACTION,
                $transaction->getId(),
            );

            $result = $this->transactionRepository->deleteById($id);

            TransactionDeletedEvent::dispatch($transaction);

            DB::commit();

            return $result;
        }
        catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
