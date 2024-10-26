@extends('layouts.auth')

@section('auth-content')
    <h1 class="text-xl font-heading font-bold mb-12">{{__('login.title')}}</h1>
    <x-form method="POST" :action="route('login')" class="font-body">
        @csrf
        <x-errors />
       
        <livewire:phone-input
            :country-code="__('global.country-code')"
            :label="__('global.phone')"
            :placeholder="__('global.phone-placeholder')"
            :error="$errors->first('phone')"
            :initial-phone="old('phone')"
            required
        />
        
        <livewire:password-input
            label="{{__('global.password')}}"
            placeholder="{{__('global.password-placeholder')}}"
            icon="o-lock-closed"
            name="password"
            :error="$errors->first('password')"
            required
        />
        
        <p class="mt-2">
            {{__('login.no-account')}}
            <a href="{{ route('register') }}" class="font-bold">{{__('login.register-here')}}</a>
        </p>

        <a href="{{route('password.forgot')}}" class="font-bold">{{__('login.forgot-password')}}</a>
        
        <x-slot:actions>
            <div class="flex flex-start flex-1 mt-2">
                <x-checkbox
                    label="{{__('login.remember-me')}}"
                    name="terms"
                    class="checkbox-base-100 border-2 whitespace-nowrap"
                    :checked="old('terms')"
                    :error="$errors->first('terms')"
                />
            </div>
            
            <livewire:button label="{{__('login.login')}}" type="submit" />
        </x-slot:actions>
    </x-form>
@endsection