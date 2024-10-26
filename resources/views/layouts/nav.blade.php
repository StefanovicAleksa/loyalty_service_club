@extends('layouts.app')

@section('content')
    
    <nav class="fixed top-0 bg-base-100 w-full flex justify-center items-center py-2 bg-gradient-to-r from-primary to-primary via-accent from-10% via-50% to-90%">
        <x-logo class="!w-24 !h-24 !m-0 rounded-full"></x-logo>
    </nav>
    <main class="max-w-md w-full fixed top-32 font-body px-4">
        @yield('main-content')
    </main>
@endsection