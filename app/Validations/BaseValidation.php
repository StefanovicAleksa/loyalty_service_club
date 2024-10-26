<?php

namespace App\Validations;

use Illuminate\Support\Facades\Validator;

abstract class BaseValidation
{
    protected function runValidation(array $data, array $rules)
    {
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        return $validator->validated();
    }

    abstract public function validate(array $data): array;
}