<?php

namespace App\Validations;

class QrCodeValidation extends BaseValidation
{
    public function validate(array $data): array
    {
        return $this->runValidation($data, [
            'customer_id' => ['required', 'exists:customers,id'],
            'offer_choice_id' => ['required', 'exists:offer_choices,id'],
        ]);
    }
}