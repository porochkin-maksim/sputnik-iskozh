<?php declare(strict_types=1);

namespace App\Services\VTB\Factories;

use App\Services\VTB\Exceptions\ErrorResponseException;
use App\Services\VTB\Responses\GetSbpResponse;
use App\Services\VTB\Responses\RegisterDoResponse;
use App\Services\VTB\Responses\SbpStatusResponse;
use Exception;

class ResponseFactory
{
    /**
     * @throws ErrorResponseException
     */
    public function throwErrorResponseException(array $data): void
    {
        throw new ErrorResponseException($data['errorMessage'], (int) $data['errorCode']);
    }

    /**
     * @throws ErrorResponseException
     */
    public function makeRegisterDoResponse(string $content): RegisterDoResponse
    {
        try {
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        }
        catch (Exception $e) {
            throw new ErrorResponseException($e->getMessage());
        }

        if (isset($data[RegisterDoResponse::ORDER_ID], $data[RegisterDoResponse::FORM_URL])) {
            return new RegisterDoResponse($data);
        }

        $this->throwErrorResponseException($data);
    }

    /**
     * @throws ErrorResponseException
     */
    public function makeGetSbpResponse(string $content): GetSbpResponse
    {
        try {
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        }
        catch (Exception $e) {
            throw new ErrorResponseException($e->getMessage());
        }

        if (isset($data[GetSbpResponse::QR_ID], $data[GetSbpResponse::PAYLOAD], $data[GetSbpResponse::QR_STATUS])) {
            return new GetSbpResponse($data);
        }

        $this->throwErrorResponseException($data);
    }

    /**
     * @throws ErrorResponseException
     */
    public function makeSbpStatusResponse(string $content): SbpStatusResponse
    {
        try {
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        }
        catch (Exception $e) {
            throw new ErrorResponseException($e->getMessage());
        }

        if (isset($data[SbpStatusResponse::QR_TYPE], $data[SbpStatusResponse::QR_STATUS], $data[SbpStatusResponse::TRANSACTION_STATE])) {
            return new SbpStatusResponse($data);
        }

        $this->throwErrorResponseException($data);
    }
}
