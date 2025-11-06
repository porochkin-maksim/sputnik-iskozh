<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Models\Provider;

use Core\Services\External\VTB\Responses\GetSbpResponse;
use Core\Services\External\VTB\Responses\RegisterDoResponse;

class VTBData implements \JsonSerializable
{
    private const string REPONSES = 'reponses';

    private const string REGISTER_DO = 'registerDo';
    private const string GET_SBP     = 'getSbp';

    private ?RegisterDoResponse $registerDoResponse;
    private ?GetSbpResponse     $getSbpResponse;

    public function __construct(
        array $data,
    )
    {
        $this->registerDoResponse = isset($data[self::REPONSES][self::REGISTER_DO])
            ? new RegisterDoResponse($data[self::REPONSES][self::REGISTER_DO])
            : null;
        $this->getSbpResponse     = isset($data[self::REPONSES][self::GET_SBP])
            ? new GetSbpResponse($data[self::REPONSES][self::GET_SBP])
            : null;
    }

    public function getRegisterDoResponse(): ?RegisterDoResponse
    {
        return $this->registerDoResponse;
    }

    public function setRegisterDoResponse(?RegisterDoResponse $registerDoResponse): VTBData
    {
        $this->registerDoResponse = $registerDoResponse;

        return $this;
    }

    public function getGetSbpResponse(): ?GetSbpResponse
    {
        return $this->getSbpResponse;
    }

    public function setGetSbpResponse(?GetSbpResponse $getSbpResponse): VTBData
    {
        $this->getSbpResponse = $getSbpResponse;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            self::REPONSES => [
                self::REGISTER_DO => $this->getRegisterDoResponse(),
                self::GET_SBP     => $this->getGetSbpResponse(),
            ],
        ];
    }
}
