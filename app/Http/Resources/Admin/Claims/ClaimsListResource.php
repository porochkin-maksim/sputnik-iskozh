<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Claims;

use lc;
use App\Http\Resources\AbstractResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Billing\Claim\Collections\ClaimCollection;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Responses\ResponsesEnum;

readonly class ClaimsListResource extends AbstractResource
{
    public function __construct(
        private ClaimCollection $claimCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = lc::roleDecorator();

        $result = [
            'claims' => [],
            'historyUrl'   => HistoryChangesLocator::route(
                type         : HistoryType::INVOICE,
                referenceType: HistoryType::CLAIM,
            ),
            'actions'      => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::CLAIMS_VIEW),
                ResponsesEnum::EDIT => $access->can(PermissionEnum::CLAIMS_EDIT),
                ResponsesEnum::DROP => $access->can(PermissionEnum::CLAIMS_DROP),
            ],
        ];

        foreach ($this->claimCollection as $claim) {
            $result['claims'][] = new ClaimResource($claim);
        }

        return $result;
    }
}
