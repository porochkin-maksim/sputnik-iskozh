<?php declare(strict_types=1);

namespace Core\Domains\Account\Models;

use Cknow\Money\Money;

readonly class AccountInfo implements \JsonSerializable
{
    public function __construct(
        private AccountDTO $account,
        private Money      $membershipFee,
        private Money      $electricTariff,
        private Money      $garbageCollectionFee,
        private Money      $roadCollectionFee,
    )
    {
    }

    public function getAccount(): AccountDTO
    {
        return $this->account;
    }

    public function getTotalMembershipFee(): Money
    {
        return $this->getMembershipFee()->multiply($this->getAccount()->getSize());
    }

    public function getMembershipFee(): Money
    {
        return $this->membershipFee;
    }

    public function getElectricTariff(): Money
    {
        return $this->electricTariff;
    }

    public function getGarbageCollectionFee(): Money
    {
        return $this->garbageCollectionFee;
    }

    public function getRoadCollectionFee(): Money
    {
        return $this->roadCollectionFee;
    }

    public function total(): Money
    {
        return $this->getTotalMembershipFee()->add(
            $this->getGarbageCollectionFee(),
            $this->getRoadCollectionFee(),
        );
    }

    public function jsonSerialize(): mixed
    {
        return [
            'account'              => $this->getAccount(),
            'totalMembershipFee'   => $this->getTotalMembershipFee(),
            'membershipFee'        => $this->getMembershipFee(),
            'electricTariff'       => $this->getElectricTariff(),
            'garbageCollectionFee' => $this->getGarbageCollectionFee(),
            'roadCollectionFee'    => $this->getRoadCollectionFee(),
        ];
    }
}
