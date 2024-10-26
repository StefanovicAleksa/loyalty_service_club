<div>
    <div role="tablist" class="h-12 tabs tabs-boxed">
        <a wire:click="setActiveTab('available')" role="tab" class="tab h-full {{ $activeTab === 'available' ? 'tab-active' : '' }}">{{__('offers.avaliable')}}</a>
        <a wire:click="setActiveTab('used')" role="tab" class="tab h-full {{ $activeTab === 'used' ? 'tab-active' : '' }}">{{__('offers.used')}}</a>
    </div>

    <div id="offersContainer" class="shadow-xl bg-base-100 backdrop-blur-md bg-opacity-50 p-4 overflow-y-auto max-h-[80vh]">
        @if($activeTab === 'available')
            <livewire:available-offers :customerId="$customerId" />
        @elseif($activeTab === 'used')
            <livewire:used-offers :customerId="$customerId" />
        @endif
    </div>
</div>