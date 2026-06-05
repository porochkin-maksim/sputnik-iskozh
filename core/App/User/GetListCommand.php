<?php declare(strict_types=1);

namespace Core\App\User;

use App\Models\Infra\UserInfo;
use App\Models\User;
use Core\Domains\User\UserSearchResponse;
use Core\Domains\User\UserSearcher;
use Core\Domains\User\UserService;
use Core\Exceptions\ValidationException;
use Core\Repositories\SearcherInterface;

readonly class GetListCommand
{
    public function __construct(
        private UserService   $userService,
        private ListValidator $validator,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(
        ?int    $limit,
        ?int    $offset,
        ?string $sortField,
        ?string $sortOrder,
        ?string $search,
        bool    $isDeleted,
        mixed   $isMemberFlag,
        bool    $isMember,
    ): UserSearchResponse
    {
        $this->validator->validate($limit, $offset, $sortField, $sortOrder);

        $searcher = new UserSearcher();
        $searcher
            ->setWithAccounts()
            ->setLimit($limit)
            ->setOffset($offset)
        ;

        if ($search) {
            $searcher->addOrWhere(User::LAST_NAME, SearcherInterface::LIKE, "{$search}%")
                ->addOrWhere(User::FIRST_NAME, SearcherInterface::LIKE, "{$search}%")
                ->addOrWhere(User::EMAIL, SearcherInterface::LIKE, "{$search}%")
                ->addOrWhere(User::PHONE, SearcherInterface::LIKE, "{$search}%")
            ;
        }
        else {
            if ($isMemberFlag !== null) {
                if ($isMember) {
                    $searcher->addWhere(UserInfo::TABLE . '.' . UserInfo::MEMBERSHIP_DATE, SearcherInterface::IS_NOT_NULL);
                }
                else {
                    $searcher->addWhere(UserInfo::TABLE . '.' . UserInfo::MEMBERSHIP_DATE, SearcherInterface::IS_NULL);
                }
            }

            if ($isDeleted) {
                $searcher->addWhere(User::SOFT_DELETED, SearcherInterface::IS_NOT_NULL);
                $searcher->setWithDeleted();
            }
        }

        if ($sortField && $sortOrder) {
            $searcher->setSortOrderProperty(
                $sortField,
                $sortOrder === 'asc' ? SearcherInterface::SORT_ORDER_ASC : SearcherInterface::SORT_ORDER_DESC,
            );
        }
        else {
            $searcher->setSortOrderProperty(User::ID, SearcherInterface::SORT_ORDER_DESC);
        }

        return $this->userService->search($searcher);
    }
}
