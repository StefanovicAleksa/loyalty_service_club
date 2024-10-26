<?php

namespace App\Livewire;

use Livewire\Component;

class PasswordInput extends Component
{
    public $password = '';
    public $label;
    public $placeholder;
    public $icon;
    public $errorField;
    public $name;

    public function mount($label = null, $placeholder = null, $icon = null, $errorField = null, $name = 'password')
    {
        $this->label = $label ?? __('global.password');
        $this->placeholder = $placeholder ?? __('global.password-placeholder');
        $this->icon = $icon;
        $this->errorField = $errorField;
        $this->name = $name;
    }

    public function render()
    {
        return view('livewire.password-input');
    }
}