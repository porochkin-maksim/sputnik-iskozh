<?php declare(strict_types=1);

namespace Core\Domains\Option\Models\DataDTO;

class ChairmanInfo implements DataDTOInterface
{
    private ?string $lastName   = null;
    private ?string $firstName  = null;
    private ?string $middleName = null;
    private ?string $phone      = null;
    private ?string $email      = null;

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): ChairmanInfo
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): ChairmanInfo
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): ChairmanInfo
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): ChairmanInfo
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): ChairmanInfo
    {
        $this->email = $email;

        return $this;
    }
    
    public function getFullName(): string
    {
        return implode(' ', [$this->getLastName(), $this->getFirstName(), $this->getMiddleName()]);
    }

    public function jsonSerialize(): array
    {
        return [
            'lastName'   => $this->getLastName(),
            'firstName'  => $this->getFirstName(),
            'middleName' => $this->getMiddleName(),
            'phone'      => $this->getPhone(),
            'email'      => $this->getEmail(),
        ];
    }
} 