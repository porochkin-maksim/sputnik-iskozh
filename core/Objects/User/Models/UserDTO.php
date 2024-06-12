<?php declare(strict_types=1);

namespace Core\Objects\User\Models;

class UserDTO
{
    private ?int    $id            = null;
    private ?string $firstName     = null;
    private ?string $middleName    = null;
    private ?string $lastName      = null;
    private ?string $email         = null;
    private ?string $password      = null;
    private ?bool   $rememberToken = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): static
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRememberToken(): ?bool
    {
        return $this->rememberToken;
    }

    public function setRememberToken(?bool $rememberToken): static
    {
        $this->rememberToken = $rememberToken;

        return $this;
    }
}
