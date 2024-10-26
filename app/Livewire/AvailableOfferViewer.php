<?php

namespace App\Livewire;

use Livewire\Component;

class AvailableOfferViewer extends Component
{
    public $offer;
    public $customerId;

    public function mount($offer, $customerId)
    {
        $this->offer = $offer;
        $this->customerId = $customerId;
    }

    public function render()
    {
        return view('livewire.available-offer-viewer');
    }
}