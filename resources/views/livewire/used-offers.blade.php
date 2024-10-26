<div>
    @forelse($usedOffers as $usedOffer)
        <livewire:used-offer-viewer :usedOffer="$usedOffer" :default-image-path="config('assets.default-offer.src')" :key="$usedOffer['redeemed_at']" />
    @empty
        <p class="text-center">{{__('offers.no-used-offers')}}</p>
    @endforelse
    {{ $usedOffers->links() }}
</div>