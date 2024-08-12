<?php declare(strict_types=1);

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Access\Enums\Permission;
use Core\Domains\Access\RoleLocator;
use Core\Domains\Access\Services\RoleService;
use Core\Domains\Report\Enums\CategoryEnum;
use Core\Domains\Report\Enums\TypeEnum;
use Core\Domains\Report\Factories\ReportFactory;
use Core\Domains\Report\ReportLocator;
use Core\Domains\Report\Requests\SaveRequest;
use Core\Domains\Report\Requests\SearchRequest;
use Core\Domains\Report\Services\FileService;
use Core\Domains\Report\Services\ReportService;
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
    private RoleService   $roleService;

    public function __construct()
    {
        $this->reportService = ReportLocator::ReportService();
        $this->reportFactory = ReportLocator::ReportFactory();
        $this->fileService   = ReportLocator::FileService();
        $this->roleService   = RoleLocator::RoleService();
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
            ->setSortOrderProperty('id', SearcherInterface::SORT_ORDER_DESC)
            ->setWithFiles();

        $reports = $this->reportService->search($searcher);

        return response()->json([
            ResponsesEnum::REPORTS => $reports,
            ResponsesEnum::EDIT    => $this->canEdit(),
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

    private function canEdit(): bool
    {
        $role = $this->roleService->getByUserId(Auth::id());

        return Permission::canEditReports($role);
    }
}
