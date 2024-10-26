<?php

namespace App\Livewire;

use Livewire\Component;

class UsedOfferInfo extends Component
{
    public string $redeemedAt;
    public string $class;

    public function mount(string $redeemedAt, string $class = '') 
    {
        $this->redeemedAt = $redeemedAt;
        $this->class = $class;
    }

    public function render()
    {
        return view('livewire.used-offer-info');
    }
}
