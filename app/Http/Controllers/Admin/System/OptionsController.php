<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Options\OptionListResource;
use Core\App\Options\SaveCommand;
use Core\Domains\Access\PermissionEnum;
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

        return view('admin.pages.options');
    }

    public function list(): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::OPTIONS_VIEW)) {
            abort(403);
        }

        $options = $this->optionService->all();

        return response()->json(new OptionListResource($options->getItems()));
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
