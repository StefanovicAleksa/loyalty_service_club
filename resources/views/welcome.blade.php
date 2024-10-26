@extends('layouts.app')

@section('content')
    <main class="max-w-md w-full px-4">
        <div class="flex flex-col items-center backdrop-blur-md bg-base-100 bg-opacity-50 p-12 shadow-xl rounded-xl">
            <x-logo class="mx-auto mb-4" />
            <h1 class="text-xl font-heading font-bold mb-12 text-center">{{ __('cafe.name') }}</h1>
            <p class="text-md font-body mb-8 text-justify hyphens-auto">{{ __('cafe.description') }}</p>
            <x-menu class="menu rounded-box w-full">
                @auth
                    @verified
                        <livewire:route-button
                            title="{{ __('menu.offers') }}"
                            icon="s-gift"
                            link="{{ route('offers.index') }}"
                            route='offers.index'
                        />
                    @else
                        <livewire:route-button
                            title="{{ __('menu.verify') }}"
                            icon="s-device-phone-mobile"
                            link="{{ route('verify.show') }}"
                            route='verify.show'
                        />
                    @endverified
                    
                    <livewire:route-button
                        title="{{ __('menu.logout') }}"
                        icon="o-user"
                        link="{{ route('logout') }}"
                        route='logout'
                    />
                @else
                    <livewire:route-button
                        title="{{ __('menu.register') }}"
                        icon="s-user"
                        link="{{ route('register') }}"
                    />
                    <livewire:route-button
                        title="{{ __('menu.login') }}"
                        icon="o-user"
                        link="{{ route('login') }}"
                    />
                @endauth
            </x-menu>
        </div>
    </main>
@endsection
