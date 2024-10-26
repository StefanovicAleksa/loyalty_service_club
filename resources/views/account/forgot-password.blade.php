@extends('layouts.auth')
@section('auth-content')
    <h1 class="text-xl font-heading font-bold mb-12">{{__('forgot-password.title')}}</h1>
    <x-form :action="route('password.reset-request')" method="POST" class="font-body">
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
       
        <x-slot:actions>
            <livewire:button label="{{__('forgot-password.send-reset-link')}}" type="submit" />
        </x-slot:actions>
    </x-form>
@endsection