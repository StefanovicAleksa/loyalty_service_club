<div class="grid gap-4 w-full">
    @if(isset($error))
        <div class="alert alert-error">{{ $error }}</div>
    @else
        @forelse($availableOffers as $offer)
            <livewire:available-offer-viewer :offer="$offer" :key="$offer->id" :customerId="$customerId" />
        @empty
            <p class="text-center">{{__('offers.no-available-offers')}}</p>
        @endforelse

        @if($availableOffers->hasMorePages())
            <button wire:click="loadMore" class="btn btn-primary">{{__('offers.available-load-more')}}Load More</button>
        @endif

        {{ $availableOffers->links() }}
    @endif
</div>