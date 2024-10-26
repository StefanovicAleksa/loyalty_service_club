<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class UsedOfferViewer extends Component
{
    public $usedOffer;
    public string $defaultImagePath;

    public function mount($usedOffer, string $defaultImagePath)
    {
        $this->usedOffer = $usedOffer;
        $this->defaultImagePath = $defaultImagePath;
    }

    public function render()
    {
        $choice = $this->usedOffer['qr_code']->offerChoice ?? null;
        $offer = $choice?->offer ?? null;
        $imagePath = $choice?->image_path;
        
        return view('livewire.used-offer-viewer', [
            'offer' => $offer,
            'choice' => $choice,
            'hasValidImage' => $imagePath && Storage::disk('public')->exists($imagePath)
        ]);
    }
}