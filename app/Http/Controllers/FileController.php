<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Core\Objects\File\FileLocator;
use Core\Objects\File\Requests\SaveRequest;
use Core\Objects\File\Requests\SearchRequest;
use Core\Objects\File\Services\FileService;
use Core\Resources\Views\ViewNames;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $searcher
            ->setSortOrderDesc()
            ->setType(null);

        $files = $this->fileService->search($searcher);

        return response()->json([
            'files' => $files,
            'edit'  => (bool) Auth::id(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        foreach ($request->allFiles() as $file) {
            $dto = $this->fileService->store($file, date('Y-m'));
            $this->fileService->save($dto);
        }

        return response()->json(true);
    }

    public function save(SaveRequest $request): JsonResponse
    {
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
        return response()->json($this->fileService->deleteById($id));
    }
}
