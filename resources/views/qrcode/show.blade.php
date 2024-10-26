@extends('layouts.nav')

@section('main-content')
    <div class="max-w-md bg-base-100 bg-opacity-50 backdrop-blur-md rounded-xl p-12 flex flex-col items-center justify-center relative shadow-xl">
        <livewire:go-back class="absolute left-4 top-4 z-10"/>
        <h1 class="text-xl font-heading font-bold mb-12">{{__('qrcode.title')}}</h1>
        <div class="max-w-md mx-auto mb-4">
            <livewire:qr-code-display :id="$qrCode->id" />
        </div>
        <p class="text-md">{{__('qrcode.id')}}: {{ $qrCode->id }}</p>
        <p class="text-md">{{__('qrcode.valid_until')}}: {{ $qrCode->valid_until }}</p>
    </div>
@endsection