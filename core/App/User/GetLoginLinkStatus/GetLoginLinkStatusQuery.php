<?php declare(strict_types=1);

namespace Core\App\User\GetLoginLinkStatus;

use App\Resources\RouteNames;
use Core\Domains\Infra\Tokens\TokenRepositoryInterface;
use Core\Domains\Infra\Uid\UidRepositoryInterface;
use Core\Domains\Infra\Uid\UidTypeEnum;

readonly class GetLoginLinkStatusQuery
{
    public function __construct(
        private readonly TokenRepositoryInterface $tokenRepository,
        private readonly UidRepositoryInterface $uidRepository,
    ) {}

    public function execute(int $userId): GetLoginLinkStatusResult
    {
        $uid   = $this->uidRepository->getUid(UidTypeEnum::LOGIN, $userId);
        $token = $this->tokenRepository->find($uid);

        if ( ! $token) {
            return new GetLoginLinkStatusResult(false);
        }

        return new GetLoginLinkStatusResult(
            hasLink  : true,
            qrLink   : route(RouteNames::ADMIN_QR_VIEW, $uid),
            tokenLink: route(RouteNames::TOKEN, $uid),
        );
    }
}
