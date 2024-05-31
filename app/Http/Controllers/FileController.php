<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Core\Objects\File\FileLocator;
use Core\Objects\File\Services\FileService;
use Illuminate\Http\JsonResponse;

class FileController extends Controller
{
    private FileService $fileService;

    public function __construct()
    {
        $this->fileService = FileLocator::FileService();
    }

    public function delete(int $id): JsonResponse
    {
        return response()->json($this->fileService->deleteById($id));
    }
}
