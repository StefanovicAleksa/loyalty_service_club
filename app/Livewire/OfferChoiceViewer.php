<?php

namespace App\Livewire;

use Livewire\Component;

class OfferChoiceViewer extends Component
{
    public $offerChoice;
    public string $defaultImagePath;
    public $customerId;
   
    public function mount($offerChoice, string $defaultImagePath, $customerId = null)
    {
        $this->offerChoice = $offerChoice;
        $this->defaultImagePath = $defaultImagePath;
        $this->customerId = $customerId;
    }

    public function render()
    {
        return view('livewire.offer-choice-viewer');
    }
}