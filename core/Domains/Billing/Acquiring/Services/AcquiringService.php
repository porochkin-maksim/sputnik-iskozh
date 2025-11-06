<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Services;

use App\Models\Billing\Acquiring;
use Core\Domains\Billing\Acquiring\Collections\AcquiringCollection;
use Core\Domains\Billing\Acquiring\Factories\AcquiringFactory;
use Core\Domains\Billing\Acquiring\Models\AcquiringDTO;
use Core\Domains\Billing\Acquiring\Repositories\AcquiringRepository;
use Core\Domains\Billing\Acquiring\Models\AcquiringSearcher;
use Core\Domains\Billing\Acquiring\Responses\SearchResponse;

readonly class AcquiringService
{
    public function __construct(
        private AcquiringFactory    $acquiringFactory,
        private AcquiringRepository $acquiringRepository,
    )
    {
    }

    public function isAcquringAvailable(): bool
    {
        return ProviderSelector::random() !== null;
    }

    public function getByInvoiceAndUserId(
        int $invoiceId,
        int $userId,
    ): AcquiringCollection
    {
        $models = $this->acquiringRepository->getByInvoiceAndUserId($invoiceId, $userId);

        return $this->acquiringFactory->makeDtoFromObjects($models);
    }

    public function save(AcquiringDTO $acquiring): AcquiringDTO
    {
        $model = $this->acquiringRepository->getById($acquiring->getId());

        $model = $this->acquiringRepository->save($this->acquiringFactory->makeModelFromDto($acquiring, $model));

        return $this->acquiringFactory->makeDtoFromObject($model);
    }

    public function getById(int $id): ?AcquiringDTO
    {
        /** @var Acquiring|null $model */
        $model = $this->acquiringRepository->getById($id);

        return $model ? $this->acquiringFactory->makeDtoFromObject($model) : null;
    }

    public function search(AcquiringSearcher $searcher): SearchResponse
    {
        $response = $this->acquiringRepository->search($searcher);

        $result = new SearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new AcquiringCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->acquiringFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
    }
}
