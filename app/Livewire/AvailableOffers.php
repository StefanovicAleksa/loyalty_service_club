<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\OfferService;
use Illuminate\Support\Facades\Log;

class AvailableOffers extends Component
{
    use WithPagination;

    public $customerId;
    public $perPage = 10;

    protected $listeners = ['offerRedeemed' => '$refresh'];

    public function mount($customerId)
    {
        $this->customerId = $customerId;
    }

    public function render()
    {
        try {
            $availableOffers = OfferService::getAvailableOffers($this->customerId, $this->perPage);

            return view('livewire.available-offers', [
                'availableOffers' => $availableOffers
            ]);
        } catch (\Exception $e) {
            Log::error("Error rendering available offers: " . $e->getMessage());
            return view('livewire.available-offers', [
                'availableOffers' => collect(),
                'error' => 'An error occurred while loading offers. Please try again later.'
            ]);
        }
    }

    public function loadMore()
    {
        $this->perPage += 10;
    }
}