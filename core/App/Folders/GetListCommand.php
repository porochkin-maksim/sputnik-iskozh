<?php declare(strict_types=1);

namespace Core\App\Folders;

use App\Models\File\FolderModel;
use Core\Domains\Folders\FolderSearcher;
use Core\Domains\Folders\FolderSearchResponse;
use Core\Domains\Folders\FolderService;
use Core\Exceptions\ValidationException;
use Core\Repositories\SearcherInterface;

readonly class GetListCommand
{
    public function __construct(
        private FolderService $folderService,
        private GetListValidator $validator,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(?int $limit, ?int $parentId): FolderSearchResponse
    {
        $this->validator->validate($limit);

        return $this->folderService->search((new FolderSearcher())
            ->setLimit($limit ?: 100)
            ->setParentId($parentId)
            ->setSortOrderProperty(FolderModel::NAME, SearcherInterface::SORT_ORDER_ASC)
        );
    }
}
