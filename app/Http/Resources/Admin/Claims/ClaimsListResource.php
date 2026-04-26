<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Claims;

use lc;
use App\Http\Resources\AbstractResource;
use App\Support\HistoryChangesRoute;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Billing\Claim\ClaimCollection;
use Core\Domains\HistoryChanges\HistoryType;

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
            'historyUrl'   => HistoryChangesRoute::make(
                type         : HistoryType::INVOICE,
                referenceType: HistoryType::CLAIM,
            ),
            'actions'      => [
                'view' => $access->can(PermissionEnum::CLAIMS_VIEW),
                'edit' => $access->can(PermissionEnum::CLAIMS_EDIT),
                'drop' => $access->can(PermissionEnum::CLAIMS_DROP),
            ],
        ];

        foreach ($this->claimCollection as $claim) {
            $result['claims'][] = new ClaimResource($claim);
        }

        return $result;
    }
}
