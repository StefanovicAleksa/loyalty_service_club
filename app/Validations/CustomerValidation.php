<?php

namespace App\Validations;

class CustomerValidation extends BaseValidation
{
    public function validate(array $data): array
    {
        return $this->runValidation($data, [
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'phone' => ['required', 'string', 'regex:/^\+[1-9]\d{1,14}$/'],
            'password' => ['required', 'string', 'min:8'],
        ]);
    }
}