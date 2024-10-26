<a href="{{ route('welcome') }}">
    <img
        src="{{ config('assets.logo.src') }}"
        alt="{{ config('assets.logo.alt') }}"
        {{ $attributes->merge(['class' => 'rounded-full mb-4 w-32 h-32 border-2 border-neutral hover:border-accent transition-colors duration-300 ' . ($class ?? '')]) }}
    >
</a>