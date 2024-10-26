<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'required|string|regex:/^\+[1-9]\d{1,14}$/',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'required|accepted',
        ];
    }

    public function attributes()
    {
        return [
            'first_name' => __('global.first_name'),
            'last_name' => __('global.last_name'),
        ];
    }
}