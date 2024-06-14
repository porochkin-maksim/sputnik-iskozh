<?php declare(strict_types=1);

namespace Core\Objects\User\Services;

use Core\Objects\User\Models\UserDTO;

class UserDecorator
{
    public function __construct(
        private ?UserDTO $user,
    )
    {
        if (!$this->user) {
            $this->user = new UserDTO();
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
        return (string) $this->user->getLastName();
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
}
