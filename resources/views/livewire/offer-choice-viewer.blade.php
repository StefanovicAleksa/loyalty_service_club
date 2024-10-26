<div class="card card-side bg-base-100 shadow-xl mb-4 min-h-min">
    <figure class="w-1/3 aspect-square">
        <div class="w-full h-full relative">
            @if($offerChoice->image_path)
                <img src="{{ asset('storage/' . $offerChoice->image_path) }}" alt="{{ $offerChoice->name }} picture" class="absolute inset-0 w-full h-full object-cover">
            @else
                <img src="{{ asset($defaultImagePath) }}" alt="{{ $offerChoice->name }} picture" class="absolute inset-0 w-full h-full object-cover">
            @endif
        </div>
    </figure>
    <div class="card-body p-4 w-2/3">
        <h2 class="card-title text-lg">{{ $offerChoice->name }}</h2>
        <p class="text-md">{{ $offerChoice->description }}</p>
        <div class="card-actions justify-end">
            @if($customerId)
                <livewire:generate-qr-code-button
                    :offer-choice-id="$offerChoice->id"
                    :customer-id="$customerId"
                />
            @endif
        </div>
    </div>
</div>