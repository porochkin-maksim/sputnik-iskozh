<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Providers\VTB;

use App\Services\VTB\Responses\GetSbpResponse;
use App\Services\VTB\Responses\RegisterDoResponse;
use JsonSerializable;

class VTBData implements JsonSerializable
{
    private const string RESPONSES = 'reponses';
    private const string REGISTER_DO = 'registerDo';
    private const string GET_SBP = 'getSbp';

    private ?RegisterDoResponse $registerDoResponse;
    private ?GetSbpResponse $getSbpResponse;

    public function __construct(array $data)
    {
        $this->registerDoResponse = isset($data[self::RESPONSES][self::REGISTER_DO])
            ? new RegisterDoResponse($data[self::RESPONSES][self::REGISTER_DO])
            : null;
        $this->getSbpResponse = isset($data[self::RESPONSES][self::GET_SBP])
            ? new GetSbpResponse($data[self::RESPONSES][self::GET_SBP])
            : null;
    }

    public function getRegisterDoResponse(): ?RegisterDoResponse
    {
        return $this->registerDoResponse;
    }

    public function setRegisterDoResponse(?RegisterDoResponse $registerDoResponse): static
    {
        $this->registerDoResponse = $registerDoResponse;

        return $this;
    }

    public function getGetSbpResponse(): ?GetSbpResponse
    {
        return $this->getSbpResponse;
    }

    public function setGetSbpResponse(?GetSbpResponse $getSbpResponse): static
    {
        $this->getSbpResponse = $getSbpResponse;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            self::RESPONSES => [
                self::REGISTER_DO => $this->getRegisterDoResponse(),
                self::GET_SBP => $this->getGetSbpResponse(),
            ],
        ];
    }
}
