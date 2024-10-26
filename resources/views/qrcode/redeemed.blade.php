@extends('layouts.app')

@section('content')
<main class="max-w-md w-full top-32 font-body px-4">
    <div class="max-w-md bg-base-100 bg-opacity-50 backdrop-blur-md rounded-xl p-12 flex flex-col items-center justify-center relative shadow-xl">
        <h1 class="text-xl font-heading font-bold mb-12">{{__('redeemed.title')}}</h1>
        <x-icon name="m-check-circle" class="w-48 h-48 text-success" />
        <p class="block mt-4 text-lg font-body text-center">{{__('redeemed.success')}}</p>
        <p class="mt-8 text-center text-md">{{__('redeemed.redirect')}}</p>
    </div>
</main>

<script>
    setTimeout(function() {
        window.location.href = "{{ $redirectUrl }}";
    }, 3000);
</script>
@endsection