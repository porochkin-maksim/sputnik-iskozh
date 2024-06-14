<?php declare(strict_types=1);

namespace Core\Domains\Account\Models;

use App\Models\Account\Account;
use Core\Db\Searcher\SearcherInterface;
use Core\Db\Searcher\SearcherTrait;

class AccountSearcher implements SearcherInterface
{
    use SearcherTrait;

    public function setPrimaryUserId(int $id): static
    {
        $this->addWhere(Account::PRIMARY_USER_ID, SearcherInterface::EQUALS, $id);
    }
}
