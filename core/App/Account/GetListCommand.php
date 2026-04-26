<?php declare(strict_types=1);

namespace Core\App\Account;

use App\Models\Account\Account;
use Core\Domains\Account\AccountSearcher;
use Core\Domains\Account\AccountService;
use Core\Exceptions\ValidationException;
use Core\Repositories\SearcherInterface;

readonly class GetListCommand
{
    public function __construct(
        private AccountService $accountService,
        private ListValidator  $validator,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(
        ?int    $limit,
        ?int    $offset,
        ?string $search,
        ?int    $accountId,
        ?string $sortField,
        ?string $sortOrder,
    ): array
    {
        $this->validator->validate($limit, $offset, $sortField, $sortOrder);

        $searcher = AccountSearcher::make()
            ->setWithUsers()
            ->setLimit($limit)
            ->setOffset($offset)
        ;

        if ($search) {
            $searcher->addWhere(Account::NUMBER, SearcherInterface::LIKE, "%{$search}%");
        }

        if ($accountId) {
            $searcher->setId($accountId);
        }

        if ($sortField && $sortOrder) {
            $searcher->setSortOrderProperty(
                $sortField,
                $sortOrder === 'asc' ? SearcherInterface::SORT_ORDER_ASC : SearcherInterface::SORT_ORDER_DESC,
            );
        }
        else {
            $searcher->setSortOrderProperty(Account::ID, SearcherInterface::SORT_ORDER_DESC);
        }

        return [
            'accounts'    => $this->accountService->search($searcher),
            'allAccounts' => $this->accountService->search(
                AccountSearcher::make()->setSortOrderProperty(Account::NUMBER, SearcherInterface::SORT_ORDER_ASC),
            ),
        ];
    }
}
