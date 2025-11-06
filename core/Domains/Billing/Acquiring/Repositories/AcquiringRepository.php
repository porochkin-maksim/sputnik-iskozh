<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Repositories;

use App\Models\Billing\Acquiring;
use Core\Db\RepositoryTrait;
use Illuminate\Database\Eloquent\Collection;

class AcquiringRepository
{
    private const string TABLE = Acquiring::TABLE;

    use RepositoryTrait {
        getById as traitGetById;
        getByIds as traitGetByIds;
    }

    protected function modelClass(): string
    {
        return Acquiring::class;
    }

    /**
     * @return Acquiring[]|Collection
     */
    public function getByInvoiceAndUserId(int $invoiceId, int $userId): array|Collection
    {
        return Acquiring::where(Acquiring::INVOICE_ID, $invoiceId)
            ->where(Acquiring::USER_ID, $userId)
            ->get()
        ;
    }
}
