<?php

namespace App\Validations;

class UserValidation extends BaseValidation
{
    public function validate(array $data): array
    {
        return $this->runValidation($data, [
            'customer_id' => ['required', 'exists:customers,id'],
            'password' => ['required', 'string', 'min:8'],
        ]);
    }
}