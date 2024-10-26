<?php

namespace App\Livewire;

use Livewire\Component;

class RouteButton extends Component
{
    public $title;
    public $icon;
    public $link;

    public function mount($title, $icon, $link)
    {
        $this->title = $title;
        $this->icon = $icon;
        $this->link = $link;
    }

    public function render()
    {
        return view('livewire.route-button');
    }
}