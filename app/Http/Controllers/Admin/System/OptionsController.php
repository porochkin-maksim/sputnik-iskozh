<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Options\SaveRequest;
use App\Http\Resources\Admin\Options\OptionListResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Option\Enums\OptionEnum;
use Core\Domains\Option\Factories\OptionFactory;
use Core\Domains\Option\OptionLocator;
use Core\Domains\Option\Services\OptionService;
use Core\Resources\Views\ViewNames;
use Core\Responses\ResponsesEnum;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use lc;

class OptionsController extends Controller
{
    private OptionService $optionService;
    private OptionFactory $optionFactory;

    public function __construct()
    {
        $this->optionService = OptionLocator::OptionService();
        $this->optionFactory = OptionLocator::OptionFactory();
    }

    public function index(): View
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::OPTIONS_VIEW)) {
            abort(403);
        }

        return view(ViewNames::ADMIN_PAGES_OPTIONS);
    }

    public function list(): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::OPTIONS_VIEW)) {
            abort(403);
        }

        $options = $this->optionService->all();

        return response()->json(new OptionListResource($options->getItems()));
    }

    public function save(SaveRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::OPTIONS_EDIT)) {
            abort(403);
        }

        $option = $this->optionService->getById($request->getId());
        if ( ! $option) {
            abort(404);
        }

        $dataDto = $this->optionFactory->makeDataDtoFromArray(
            OptionEnum::tryFrom($request->getId()),
            $request->getData()
        );
        
        $option->setData($dataDto);
        $option = $this->optionService->save($option);

        return response()->json([
            ResponsesEnum::OPTION => $option,
        ]);
    }
}
