<div class="{{$class}}">
    <x-dropdown hint="offer information">
        <x-slot:trigger>
            <x-icon name="o-information-circle" class="w-8" />
        </x-slot:trigger>
        <x-menu-item title="{{__('offers.redeemed_at')}}: {{$redeemedAt}}" />
    </x-dropdown>
</div>

