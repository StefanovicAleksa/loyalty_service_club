<div class="card card-side bg-base-100 bg-opacity-50 shadow-xl mb-4 min-h-min relative">
    @if($offer)
        <livewire:used-offer-info 
            :redeemed-at="$usedOffer['redeemed_at']"
            :offer="$offer"
            class="absolute top-4 right-4"
        />
        
        <figure class="w-1/3 aspect-square">
            <div class="w-full h-full relative">
                @if($hasValidImage && $choice)
                    <img 
                        src="{{ asset('storage/' . $choice->image_path) }}" 
                        alt="{{ $choice->name }}" 
                        class="absolute inset-0 w-full h-full object-cover"
                    >
                @else
                    <img 
                        src="{{ asset($defaultImagePath) }}" 
                        alt="{{ $choice?->name ?? 'Default' }}" 
                        class="absolute inset-0 w-full h-full object-cover"
                    >
                @endif
            </div>
        </figure>
        
        <div class="card-body p-4 w-2/3">
            <h2 class="card-title text-lg border-b-2 pb-2 border-secondary">
                {{ $offer->name }}
            </h2>
            @if($choice)
                <h3 class="card-title text-lg">{{ $choice->name }}</h3>
                <p class="text-md">{{ $choice->description }}</p>
            @endif
        </div>
    @else
        <div class="card-body">
            <p class="text-error">{{ __('offers.offer_not_found') }}</p>
        </div>
    @endif
</div>