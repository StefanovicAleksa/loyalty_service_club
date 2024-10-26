<div class="{{$class}}">
    <x-dropdown>
        <x-slot:trigger>
            <x-button class="btn btn-circle">
                <x-icon name="s-user" class="w-8"></x-icon>
            </x-button>
        </x-slot:trigger>
        @verified
        @else
            <x-menu-item title="{{__('menu.verify')}}" icon="s-device-phone-mobile" link="{{route('verify.show')}}" route="verify.show" />
        @endverified
        <x-menu-item title="{{__('change-password.change-password')}}" icon="s-key" link="{{route('password.change')}}" route="password.change" />
        <x-menu-separator />
        <x-menu-item title="{{__('menu.logout')}}" icon="c-arrow-left-end-on-rectangle" link="{{route('logout')}}" route="logout" />
    </x-dropdown>
</div>