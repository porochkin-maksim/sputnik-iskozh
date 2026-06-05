<?php declare(strict_types=1);

namespace App\Http\Requests\Public;

use App\Http\Requests\DefaultRequest;

class PublicConsentRequest extends DefaultRequest
{
    public function rules(): array
    {
        return [
            'consent' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'consent.required' => 'Необходимо согласие на обработку персональных данных.',
            'consent.accepted' => 'Необходимо согласие на обработку персональных данных.',
        ];
    }
}
