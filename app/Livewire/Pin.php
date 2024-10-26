<?php

namespace App\Livewire;

use Livewire\Component;

class Pin extends Component
{
    public $uuid;
    public $size;
    public $numeric;
    public $name;
    public $value;
    public $class;

    public function __construct($size = 6, $numeric = false, $name = 'pin', $value = '', $class = '')
    {
        $this->uuid = uniqid('pin_');
        $this->size = $size;
        $this->numeric = $numeric;
        $this->name = $name;
        $this->value = $value;
        $this->class = $class;
    }

    public function render()
    {
        return view('livewire.pin');
    }
}