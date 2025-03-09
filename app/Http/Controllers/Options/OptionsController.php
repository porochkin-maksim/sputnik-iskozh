<?php declare(strict_types=1);

namespace App\Http\Controllers\Options;

use App\Http\Controllers\Controller;
use Core\Domains\Access\Enums\Permission;
use Core\Domains\Access\RoleLocator;
use Core\Domains\Access\Services\RoleService;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\OptionLocator;
use Core\Domains\Option\Services\OptionService;
use Core\Domains\Option\Requests\SaveRequest;
use Core\Domains\Option\Requests\SearchRequest;
use Core\Resources\Views\ViewNames;
use Core\Responses\ResponsesEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OptionsController extends Controller
{
    private OptionService $optionService;
    private RoleService   $roleService;

    public function __construct()
    {
        $this->optionService = OptionLocator::OptionService();
        $this->roleService   = RoleLocator::RoleService();
    }

    private function checkAccess(): void
    {
        $role = $this->roleService->getByUserId(Auth::id());

        // if ( ! Permission::canEditOptions($role)) {
        //     abort(403);
        // }
    }

    public function index(): View
    {
        $this->checkAccess();

        return view(ViewNames::PAGES_OPTIONS_INDEX);
    }

    public function edit(int $id): JsonResponse
    {
        $this->checkAccess();

        $option = $this->optionService->getByType(OptionEnum::tryFrom($id));

        return response()->json([
            ResponsesEnum::OPTION => $option,
        ]);
    }

    public function list(SearchRequest $request): JsonResponse
    {
        $this->checkAccess();

        $searcher = $request->dto();

        $options = $this->optionService->search($searcher);

        return response()->json([
            ResponsesEnum::OPTIONS => $options->getItems(),
            ResponsesEnum::TOTAL   => $options->getTotal(),
        ]);
    }

    public function save(SaveRequest $request): JsonResponse
    {
        $this->checkAccess();

        $option = $request->dto();
        $option = $this->optionService->save($option);

        return response()->json($option);
    }
}
