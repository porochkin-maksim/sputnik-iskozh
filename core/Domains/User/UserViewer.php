<?php declare(strict_types=1);

namespace Core\Domains\User;

use App\Resources\RouteNames;
use Core\Domains\Access\PermissionEnum;

class UserViewer
{
    public function __construct(
        private ?UserEntity $user,
    )
    {
        if ( ! $this->user) {
            $this->user = new UserEntity();
        }
    }

    public function getEmail(): string
    {
        return (string) $this->user->getEmail();
    }

    public function getLastName(): string
    {
        return (string) $this->user->getLastName();
    }

    public function getFirstName(): string
    {
        return (string) $this->user->getFirstName();
    }

    public function getMiddleName(): string
    {
        return (string) $this->user->getMiddleName();
    }

    public function getFullName(): string
    {
        return trim(sprintf('%s %s %s', $this->getLastName(), $this->getFirstName(), $this->getMiddleName()));
    }

    public function getShortName(): string
    {
        $lastName   = $this->getLastName();
        $firstName  = $this->getFirstName() ? mb_substr($this->getFirstName(), 0, 1) . '.' : '';
        $middleName = $this->getMiddleName() ? mb_substr($this->getMiddleName(), 0, 1) . '.' : '';

        return trim(sprintf('%s %s%s', $lastName, $firstName, $middleName));
    }

    public function getDisplayName(bool $short = true): string
    {
        if ($short) {
            return $this->getShortName() ? : $this->getEmail();
        }

        return $this->getFullName() ? : $this->getEmail();
    }

    public function getAdminViewUrl(): ?string
    {
        if (\lc::roleDecorator()->can(PermissionEnum::USERS_VIEW)) {
            return route(RouteNames::ADMIN_USER_VIEW, $this->user->getId());
        }

        return null;
    }
}
