<?php declare(strict_types=1);

namespace Core\Objects\User\Services;

use App\Models\User;

class UserDecorator
{
    public function __construct(
        private ?User $user,
    )
    {
        if (!$this->user) {
            $this->user = new User();
        }
    }

    public function getEmail(): string
    {
        return $this->user->email;
    }

    public function getLastName(): string
    {
        return (string) $this->user->last_name;
    }

    public function getFirstName(): string
    {
        return (string) $this->user->first_name;
    }

    public function getMiddleName(): string
    {
        return (string) $this->user->middle_name;
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
