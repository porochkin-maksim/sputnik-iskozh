<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Options\OptionListResource;
use App\Support\HistoryChangesRoute;
use Core\App\Options\SaveCommand;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Domains\Option\OptionService;
use Core\Exceptions\ValidationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use lc;

class OptionsController extends Controller
{

    public function __construct(
        private readonly OptionService $optionService,
        private readonly SaveCommand $saveCommand,
    )
    {
    }

    public function index(): View
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::OPTIONS_VIEW)) {
            abort(403);
        }

        return view('pages.admin.system.options');
    }

    public function list(): JsonResponse
    {
        $roleDecorator = lc::roleDecorator();

        if ( ! $roleDecorator->can(PermissionEnum::OPTIONS_VIEW)) {
            abort(403);
        }

        $options = $this->optionService->all();

        return response()->json([
            'options'    => new OptionListResource($options->getItems()),
            'historyUrl' => HistoryChangesRoute::make(type: HistoryType::OPTION),
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function save(DefaultRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::OPTIONS_EDIT)) {
            abort(403);
        }

        $option = $this->saveCommand->execute(
            $request->getInt('id'),
            $request->getArray('data'),
        );

        return response()->json([
            'option' => $option,
        ]);
    }
}
