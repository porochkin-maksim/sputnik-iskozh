<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Claims\ClaimResource;
use App\Http\Resources\Admin\Claims\ClaimsListResource;
use App\Http\Resources\Admin\Claims\ServicesListResource;
use App\Http\Resources\Common\SelectResource;
use Core\App\Billing\Claim\GetFormDataCommand;
use Core\App\Billing\Claim\GetListCommand;
use Core\App\Billing\Claim\SaveCommand;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Billing\Claim\ClaimService;
use Illuminate\Http\JsonResponse;
use lc;

class ClaimController extends Controller
{

    public function __construct(
        private readonly ClaimService       $claimService,
        private readonly GetFormDataCommand $getFormDataCommand,
        private readonly GetListCommand     $getListCommand,
        private readonly SaveCommand        $saveCommand,
    )
    {
    }

    public function create(int $invoiceId): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::CLAIMS_EDIT)) {
            abort(403);
        }

        $formData = $this->getFormDataCommand->create($invoiceId);
        if ($formData === null) {
            abort(412);
        }

        return response()->json([
            'servicesSelect' => new SelectResource($formData->servicesSelect),
            'claim'          => new ClaimResource($formData->claim),
            'services'       => new ServicesListResource($formData->services),
        ]);
    }

    public function get(int $invoiceId, int $claimId): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::CLAIMS_VIEW)) {
            abort(403);
        }
        if ( ! $invoiceId || ! $claimId) {
            abort(412);
        }

        $formData = $this->getFormDataCommand->get($invoiceId, $claimId);
        if ($formData === null) {
            abort(412);
        }

        return response()->json([
            'servicesSelect' => new SelectResource($formData->servicesSelect),
            'claim'          => new ClaimResource($formData->claim),
            'services'       => new ServicesListResource($formData->services),
        ]);
    }

    public function save(int $invoiceId, DefaultRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::CLAIMS_EDIT)) {
            abort(403);
        }

        $claim = $this->saveCommand->execute(
            id       : $request->getIntOrNull('id'),
            invoiceId: $request->getIntOrNull('invoice_id'),
            serviceId: $request->getIntOrNull('service_id'),
            tariff   : $request->getFloat('tariff'),
            cost     : $request->getFloat('cost'),
            name     : $request->getStringOrNull('name'),
        );

        if ($claim === null) {
            abort(404);
        }

        return response()->json([
            'claim' => new ClaimResource($claim),
        ]);
    }

    public function list(int $invoiceId): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::CLAIMS_VIEW)) {
            abort(403);
        }

        $claims = $this->getListCommand->execute($invoiceId);
        if ($claims === null) {
            abort(412);
        }

        return response()->json(new ClaimsListResource(
            $claims,
        ));
    }

    public function delete(int $invoiceId, int $id): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::CLAIMS_DROP)) {
            abort(403);
        }

        return $this->claimService->deleteById($id);
    }
}
