@extends('layouts.auth')
@section('auth-content')
    <h1 class="text-xl font-heading font-bold mb-12">{{__('verify-identity.title')}}</h1>
    
    <x-form :action="route('password.verify-identity', ['phone' => $phone])" method="POST" class="font-body max-w-md">
        @csrf
        <x-errors />
       
        <div class="flex flex-col items-center font-body">
            <x-icon name="o-lock-closed" class="w-24 h-24 mb-4" />
            <p class="text-md font-body mb-6">{{ __('verify-identity.instructions') }}</p>
            <livewire:pin wire:model="otp" :size="6" class="mb-12 rounded-xl border-accent border-2 shadow-lg" name="otp" numeric />
            <livewire:button label="{{__('verify-identity.verify')}}" class="w-32 shadow-lg" type="submit" />
            <div class="mt-4 font-body">
                <livewire:otp-resend :phone="$phone" />
            </div>
        </div>
    </x-form>
@endsection