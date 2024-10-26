<?php

namespace App\Livewire;

use Livewire\Component;

class Button extends Component
{
    public $label;
    public $class;
    public $type = 'button';

    public function mount($label, $class = '', $type = 'button')
    {
        $this->label = $label;
        $this->class = $class;
        $this->type = $type;
    }

    public function handleClick()
    {
        if ($this->action) {
            $this->dispatch($this->action);
        }
    }

    public function render()
    {
        return view('livewire.button');
    }
}