@extends('layouts.auth')

@section('auth-content')
    <h1 class="text-xl font-heading font-bold mb-12 text-center">{{__('change-password.title')}}</h1>
    <x-form :action="route('password.change')" method="POST" class="font-body">
        @csrf
        <x-errors />
        
        <livewire:password-input
            label="{{__('global.new-password')}}"
            placeholder="{{__('global.new-password-placeholder')}}"
            icon="o-lock-closed"
            name="password"
            :error="$errors->first('password')"
        />
        
        <livewire:password-input
            label="{{__('global.confirm-password')}}"
            placeholder="{{__('global.confirm-new-password-placeholder')}}"
            icon="o-lock-closed"
            name="password_confirmation"
            :error="$errors->first('password_confirmation')"
        />
        
        <x-slot:actions>
            <livewire:button label="{{__('change-password.change-password')}}" type="submit" />
        </x-slot:actions>
    </x-form>
@endsection