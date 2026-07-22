<?php declare(strict_types=1);

namespace Core\App\User\MakeLoginLink;

use App\Resources\RouteNames;
use Core\Domains\Infra\Tokens\TokenRepositoryInterface;
use Core\Domains\Infra\Uid\UidRepositoryInterface;
use Core\Domains\Infra\Uid\UidTypeEnum;
use Illuminate\Support\Facades\Hash;

readonly class MakeLoginLinkCommand
{
    public function __construct(
        private readonly TokenRepositoryInterface $tokenRepository,
        private readonly UidRepositoryInterface $uidRepository,
    ) {}

    public function execute(MakeLoginLinkInput $input): MakeLoginLinkResult
    {
        $pin = $input->pin ?? (string) random_int(100000, 999999);

        $uid = $this->uidRepository->getUid(UidTypeEnum::LOGIN, $input->userId);
        $this->tokenRepository->save(['pin' => Hash::make($pin)], $uid);

        return new MakeLoginLinkResult(
            qrLink: route(RouteNames::ADMIN_QR_VIEW, $uid),
            tokenLink: route(RouteNames::TOKEN, $uid),
            pin: $pin,
        );
    }
}
