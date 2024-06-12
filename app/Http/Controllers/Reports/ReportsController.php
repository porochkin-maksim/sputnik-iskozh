<?php declare(strict_types=1);

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Core\Objects\Report\Enums\CategoryEnum;
use Core\Objects\Report\Enums\TypeEnum;
use Core\Objects\Report\Factories\ReportFactory;
use Core\Objects\Report\Models\ReportSearcher;
use Core\Objects\Report\ReportLocator;
use Core\Objects\Report\Requests\SaveRequest;
use Core\Objects\Report\Requests\SearchRequest;
use Core\Objects\Report\Services\FileService;
use Core\Objects\Report\Services\ReportService;
use Core\Resources\Views\ViewNames;
use Core\Responses\ResponsesEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    private ReportService $reportService;
    private ReportFactory $reportFactory;
    private FileService   $fileService;

    public function __construct()
    {
        $this->reportService = ReportLocator::ReportService();
        $this->reportFactory = ReportLocator::ReportFactory();
        $this->fileService   = ReportLocator::FileService();
    }

    public function index(): View
    {
        return view(ViewNames::PAGES_REPORTS_INDEX);
    }

    public function create(): JsonResponse
    {
        $report = $this->reportFactory->makeDefault();

        return response()->json([
            ResponsesEnum::CATEGORIES => $this->getCategories(),
            ResponsesEnum::TYPES      => $this->getTypes(),
            ResponsesEnum::REPORT     => $report,
        ]);
    }

    public function edit(int $id): JsonResponse
    {
        $report = $this->reportService->getById($id);

        return response()->json([
            ResponsesEnum::CATEGORIES => $this->getCategories(),
            ResponsesEnum::TYPES      => $this->getTypes(),
            ResponsesEnum::REPORT     => $report,
        ]);
    }

    public function list(SearchRequest $request): JsonResponse
    {
        $searcher = $request->dto();
        $searcher
            ->setSortOrderDesc()
            ->setWithFiles();

        $reports = $this->reportService->search($searcher);

        return response()->json([
            ResponsesEnum::REPORTS => $reports,
            ResponsesEnum::EDIT    => (bool) Auth::id(),
        ]);
    }

    public function save(SaveRequest $request): JsonResponse
    {
        $report = $request->dto();
        $report = $this->reportService->save($report);

        return response()->json($report);
    }

    public function delete(int $id): JsonResponse
    {
        return response()->json($this->reportService->deleteById($id));
    }

    public function uploadFile(int $id, Request $request): JsonResponse
    {
        foreach ($request->allFiles() as $file) {
            $this->fileService->save($file, $id);
        }

        return response()->json(true);
    }

    private function getCategories(): array
    {
        return CategoryEnum::json();
    }

    private function getTypes(): array
    {
        return TypeEnum::json();
    }
}
