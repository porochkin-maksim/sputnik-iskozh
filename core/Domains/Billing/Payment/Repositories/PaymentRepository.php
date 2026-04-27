<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment\Repositories;

use App\Models\Account\Account;
use App\Models\Billing\Payment;
use Core\Db\RepositoryTrait;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Billing\Payment\Collections\PaymentCollection;
use Core\Domains\Billing\Payment\Factories\PaymentFactory;
use Core\Domains\Billing\Payment\Models\PaymentDTO;
use Core\Domains\Billing\Payment\Responses\PaymentSearchResponse;
use Illuminate\Database\Eloquent\Builder;

class PaymentRepository
{
    use RepositoryTrait;

    private const string TABLE = Payment::TABLE;

    public function __construct(
        private readonly PaymentFactory $factory,
    )
    {
    }

    protected function modelClass(): string
    {
        return Payment::class;
    }

    public function search(SearcherInterface $searcher): PaymentSearchResponse
    {
        $response   = $this->searchModels($searcher);
        $collection = new PaymentCollection();
        foreach ($response->getItems() as $model) {
            $collection->add($this->factory->makeDtoFromObject($model));
        }

        $result = new PaymentSearchResponse();
        $result->setTotal($response->getTotal())
            ->setItems($collection)
        ;

        return $result;
    }

    public function getById(?int $id): ?PaymentDTO
    {
        /** @var null|Payment $model */
        $model = $this->getModelById($id);

        return $model ? $this->factory->makeDtoFromObject($model) : null;
    }

    public function save(PaymentDTO $dto): PaymentDTO
    {
        $model = $this->getModelById($dto->getId());
        $model = $this->factory->makeModelFromDto($dto, $model);
        $model->save();

        return $this->factory->makeDtoFromObject($model);
    }

    private function getQuery(Builder $query): Builder
    {
        $query->select(self::TABLE. '.*')
            ->leftJoin(Account::TABLE, Account::TABLE . '.' . Account::ID, SearcherInterface::EQUALS, Payment::TABLE . '.' . Payment::ACCOUNT_ID)
            ->selectSub(Account::TABLE . '.' . Account::NUMBER, 'account_number')
        ;

        return $query;
    }
}
