<?php declare(strict_types=1);

namespace App\Http\Controllers\Files;

use App\Http\Controllers\Controller;
use Core\Domains\File\FileLocator;
use Core\Domains\File\Requests\File\ChangeOrderRequest;
use Core\Domains\File\Requests\File\MoveRequest;
use Core\Domains\File\Requests\File\ReplaceRequest;
use Core\Domains\File\Requests\File\SaveRequest;
use Core\Domains\File\Requests\File\SearchRequest;
use Core\Domains\File\Requests\File\StoreRequest;
use Core\Domains\File\Services\FileService;
use Core\Resources\Views\ViewNames;
use Core\Responses\ResponsesEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class FileController extends Controller
{
    private FileService $fileService;

    public function __construct()
    {
        $this->fileService = FileLocator::FileService();
    }

    public function index(): View
    {
        return view(ViewNames::PAGES_FILES_INDEX);
    }

    public function list(SearchRequest $request): JsonResponse
    {
        $searcher = $request->dto();
        $searcher->setType(null);

        $files = $this->fileService->search($searcher)->getItems();

        return response()->json([
            ResponsesEnum::FILES => $files,
            ResponsesEnum::EDIT  => $this->canEdit(),
        ]);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        foreach ($request->allFiles() as $file) {
            $dto = $this->fileService->store($file, 'uploads/' . date('Y-m'));
            $dto->setParentId($request->getParentId());
            $this->fileService->save($dto);
        }

        return response()->json(true);
    }

    public function replace(ReplaceRequest $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        $file = $this->fileService->getById($request->getFileId());

        if ($file) {
            $newFile = $this->fileService->store($request->getFile(), 'uploads/' . date('Y-m'));
            $this->fileService->replace($file, $newFile);
        }

        return response()->json(true);
    }

    public function save(SaveRequest $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        $file = $this->fileService->getById($request->getId());
        if ($file) {
            $file->setName($request->getName());
            $this->fileService->save($file);

            return response()->json(true);
        }

        return response()->json(true);
    }

    public function delete(int $id): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        return response()->json($this->fileService->deleteById($id));
    }

    public function up(int $id, ChangeOrderRequest $request): void
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        $file = $this->fileService->getById($id);
        if ($file) {
            $this->fileService->saveFileOrderIndex($file, $request->getIndex() - 1);
        }
    }

    public function down(int $id, ChangeOrderRequest $request): void
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        $file = $this->fileService->getById($id);
        if ($file) {
            $this->fileService->saveFileOrderIndex($file, $request->getIndex() + 1);
        }
    }

    public function move(MoveRequest $request): JsonResponse
    {
        if ( ! $this->canEdit()) {
            abort(403);
        }

        $file = $this->fileService->getById($request->getFileId());
        if ($file->getType()) {
            return response()->json(false);
        }

        if ($request->isCopyType()) {
            $file = $this->fileService->copy($file);
        }

        $file->setParentId($request->getFolderId());
        $this->fileService->save($file);

        return response()->json(true);
    }

    private function canEdit(): bool
    {
        return \app::roleDecorator()->canEditFiles();
    }
}
