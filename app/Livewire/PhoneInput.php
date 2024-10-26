<?php

namespace App\Livewire;

use Livewire\Component;

class PhoneInput extends Component
{
    public $phone = '';
    public $countryCode;
    public $label;
    public $placeholder;
    public $errorField;
    public $digitNumber;
    public $formatPattern;

    public function mount($countryCode = null, $label = null, $placeholder = null, $errorField = null, $initialPhone = '', $digitNumber = null, $formatPattern = null)
    {
        $this->countryCode = $countryCode ?? __('global.country-code');
        $this->label = $label ?? __('global.phone');
        $this->placeholder = $placeholder ?? __('global.phone-placeholder');
        $this->errorField = $errorField ?? 'phone';
        $this->digitNumber = $digitNumber ?? __('global.phone-digit-number');
        $this->formatPattern = $formatPattern ?? __('global.phone-display-pattern');
        $this->phone = $initialPhone;
    }

    public function render()
    {
        return view('livewire.phone-input');
    }
}