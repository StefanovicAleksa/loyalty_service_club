<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;

class OffersTabs extends Component
{
    #[Url]
    public $activeTab = 'available';

    public $customerId;

    public function mount()
    {
        $this->customerId = Auth::user()->customer_id;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.offers-tabs');
    }
}