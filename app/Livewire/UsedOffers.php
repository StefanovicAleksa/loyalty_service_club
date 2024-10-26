<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\OfferService;

class UsedOffers extends Component
{
    use WithPagination;

    public $customerId;

    public function mount($customerId)
    {
        $this->customerId = $customerId;
    }

    public function render()
    {
        $usedOffers = OfferService::getAllUsedOrders($this->customerId, 10);

        return view('livewire.used-offers', [
            'usedOffers' => $usedOffers
        ]);
    }
}