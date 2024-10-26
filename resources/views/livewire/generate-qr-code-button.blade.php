<div>
    <x-button 
        wire:click="generateQrCode" 
        class="btn btn-circle bg-gradient-to-tr from-base-100 to-primary hover:to-accent {{$class}}"
        wire:loading.attr="disabled"
        wire:target="generateQrCode"
    >
        <div wire:loading.remove wire:target="generateQrCode">
            <x-icon name="s-qr-code" class="w-8"/>
        </div>
        <div wire:loading wire:target="generateQrCode">
            <x-icon name="o-arrow-path" class="w-8 animate-spin"/>
        </div>
    </x-button>

    @if ($errorMessage)
        <div class="alert alert-danger mt-4">
            {{ $errorMessage }}
        </div>
    @endif
</div>