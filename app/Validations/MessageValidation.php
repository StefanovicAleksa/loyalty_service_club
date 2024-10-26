<?php

namespace App\Validations;

class MessageValidation extends BaseValidation
{
    public function validate(array $data): array
    {
        return $this->runValidation($data, [
            'content' => ['required', 'string', 'max:1000'],
        ]);
    }
}