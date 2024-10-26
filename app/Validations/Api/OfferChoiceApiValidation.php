<?php

namespace App\Validations\Api;

use App\Validations\BaseValidation;

class OfferChoiceApiValidation extends BaseValidation
{
    public function validate(array $data): array
    {
        return $this->runValidation($data, [
            'offer_id' => ['sometimes', 'exists:offers,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'picture' => ['nullable', 'file', 'image', 'max:8192'], // 2MB max
        ]);
    }
}