<?php declare(strict_types=1);

namespace App\Http\Resources\Users;

use App\Http\Resources\AbstractResource;
use Core\Domains\User\Models\UserDTO;

readonly class UserResource extends AbstractResource
{
    public function __construct(
        private UserDTO $user,
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id'         => $this->user->getId(),
            'firstName'  => $this->user->getFirstName(),
            'middleName' => $this->user->getMiddleName(),
            'lastName'   => $this->user->getLastName(),
            'email'      => $this->user->getEmail(),
            // 'viewUrl'       => $this->user->getId() ? route(RouteNames::ADMIN_INVOICE_VIEW, ['id' => $this->user->getId()]) : null,
            // 'historyUrl'    => $this->user->getId() ? route(RouteNames::HISTORY_CHANGES, [
            //     'type'      => HistoryType::INVOICE,
            //     'primaryId' => $this->user->getId(),
            // ]) : null,
        ];
    }
}
