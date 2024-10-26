@extends('layouts.auth')

@section('auth-content')
    <h1 class="text-xl font-heading font-bold mb-12 text-center">{{__('register.title')}}</h1>
    <x-form :action="route('register')" method="POST" class="font-body">
        @csrf
        <x-errors />
        
        <x-input 
            label="{{__('global.first_name')}}" 
            name="first_name" 
            placeholder="{{__('global.placeholder-first-name')}}" 
            value="{{ old('first_name') }}"
            error="{{ $errors->first('first_name') }}"
            required
        />
    
        <x-input 
            label="{{__('global.last_name')}}" 
            name="last_name" 
            placeholder="{{__('global.placeholder-last-name')}}" 
            value="{{ old('last_name') }}"
            error="{{ $errors->first('last_name') }}"
            required
        />
       
        <livewire:phone-input
            :country-code="__('global.country-code')"
            :label="__('global.phone')"
            :placeholder="__('global.phone-placeholder')"
            :error="$errors->first('phone')"
            :initial-phone="old('phone')"
        />
        
        <livewire:password-input
            label="{{__('global.password')}}"
            placeholder="{{__('global.password-placeholder')}}"
            icon="o-lock-closed"
            name="password"
            :error="$errors->first('password')"
        />
        
        <livewire:password-input
            label="{{__('global.confirm-password')}}"
            placeholder="{{__('global.confirm-password-placeholder')}}"
            icon="o-lock-closed"
            name="password_confirmation"
            :error="$errors->first('password_confirmation')"
        />
        
        <p class="mt-2">
            {{__('register.already-have-account-text')}}
            <a href="{{ route('login') }}" class="font-bold">{{__('register.login-here')}}</a>
        </p>
    
        <x-checkbox 
            label="{{__('register.accept-terms-conditions')}}" 
            name="terms" 
            class="checkbox-base-100 border-2 mb-2" 
            :checked="old('terms')"
            :error="$errors->first('terms')"
            requred
        />
        
        <x-slot:actions>
            <livewire:button label="{{__('register.register')}}" type="submit" />
        </x-slot:actions>
    </x-form>
@endsection