<div class="rounded-xl shadow-xl bg-primary bg-opacity-50 p-4 relative">
    <h3 class="text-xl font-heading font-bold">{{ $offer->name }}</h3>
    <div class="border-b-2 border-base-100 pb-2 mb-4">
        <p>{{ $offer->description }}</p>
        <livewire:offer-info
            :offer="$offer"
            class="absolute top-4 right-4"
        />
    </div>
    
    <ul>
        @foreach($offer->choices as $choice)
            <li>
                <livewire:offer-choice-viewer
                    :offer-choice="$choice"
                    :customer-id="$customerId"
                    :default-image-path="config('assets.default-offer.src')"
                />
            </li>
        @endforeach
    </ul>
</div>