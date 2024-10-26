@extends('layouts.app')

@section('content')
    <main class="max-w-md w-full px-4">
        <div class="max-w-md bg-base-100 bg-opacity-50 backdrop-blur-md rounded-xl p-12 flex flex-col items-center justify-center relative shadow-xl">
            <livewire:go-back class="absolute left-4 top-4 z-10"/>
            <x-logo />
            @yield('auth-content')
        </div>
    </main>
@endsection