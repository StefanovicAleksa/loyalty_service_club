<div x-data="{ cooldownRemaining: @entangle('cooldownRemaining'), countdown: null }"
     x-init="
        $watch('cooldownRemaining', value => {
            if (value > 0) {
                clearInterval(countdown);
                countdown = setInterval(() => {
                    if (cooldownRemaining > 0) {
                        cooldownRemaining--;
                    } else {
                        clearInterval(countdown);
                    }
                }, 1000);
            }
        });
     ">
    <button wire:click="sendOtp"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50 cursor-not-allowed"
            class="font-bold hover:text-accent transition-colors duration-200"
            :class="{ 'opacity-50 cursor-not-allowed': cooldownRemaining > 0 }"
            x-bind:disabled="cooldownRemaining > 0">
        <span wire:loading.remove>
            <span x-show="cooldownRemaining > 0">
                {{ __('verify.resend-otp') }} (<span x-text="cooldownRemaining"></span>)
            </span>
            <span x-show="cooldownRemaining === 0">
                {{ $hasSentOnce ? __('verify.resend-otp') : __('verify.send-otp') }}
            </span>
        </span>
        <span wire:loading>{{ __('verify.resending') }}</span>
    </button>

    @if($error)
        <p class="text-error mt-2">{{ $error }}</p>
    @endif
</div>
