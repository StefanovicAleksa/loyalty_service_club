<?php

namespace App\Livewire;

use Livewire\Component;

class GoBack extends Component
{
    public $label = '';
    public $class = '';

    public function mount($label = null, $class = '')
    {
        $this->label = $label ?? $this->label;
        $this->class = $class;
    }

    public function render()
    {
        return view('livewire.go-back');
    }
}