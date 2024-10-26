<div class="{{$class}}">
    <x-dropdown hint="offer information">
        <x-slot:trigger>
            <x-icon name="o-information-circle" class="w-8" />
        </x-slot:trigger>
        <x-menu-item title="{{__('offers.valid_until')}}: {{$validUntil}}" />
        <x-menu-item title="{{__('offers.type')}}: {{$offerType}}" />
        @if($isPeriodic)
            @if($periodicity)
                <x-menu-item title="{{__('offers.periodicity')}}: {{$periodicity}}" />
            @endif
            @if($dayName)
                <x-menu-item title="{{__('offers.day_of_week')}}: {{$dayName}}" />
            @endif
            @if($timeRange)
                <x-menu-item title="{{__('offers.available_time')}}: {{$timeRange}}" />
            @endif
        @endif
    </x-dropdown>
</div>