<?php declare(strict_types=1);

namespace Core\App\Files;

use App\Models\Files\FileModel;
use Core\Domains\Files\FileSearcher;
use Core\Domains\Files\FileSearchResponse;
use Core\Domains\Files\FileService;
use Core\Exceptions\ValidationException;
use Core\Repositories\SearcherInterface;

readonly class GetListCommand
{
    public function __construct(
        private FileService $fileService,
        private GetListValidator $validator,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(
        ?int    $limit,
        ?int    $parentId,
        ?string $sortField,
        bool    $sortDesc,
    ): FileSearchResponse
    {
        $this->validator->validate($limit, $sortField);

        $searcher = (new FileSearcher())
            ->setType(null)
            ->setLimit($limit ? : 100)
        ;

        if ($parentId !== null) {
            $searcher->addWhere(FileModel::PARENT_ID, SearcherInterface::EQUALS, $parentId);
        }
        else {
            $searcher->addWhere(FileModel::PARENT_ID, SearcherInterface::IS_NULL);
        }

        if ($sortField) {
            $searcher->setSortOrderProperty($sortField, $sortDesc ? SearcherInterface::SORT_ORDER_DESC : SearcherInterface::SORT_ORDER_ASC);
        }

        return $this->fileService->search($searcher);
    }
}
