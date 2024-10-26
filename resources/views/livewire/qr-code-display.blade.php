<div 
    x-data="{ 
        showExpiredMessage: false, 
        isRedeemed: false,
        pollInterval: 2000,
        checkRedemption: async function() {
            if (this.isRedeemed) return;

            try {
                const result = await $wire.checkRedemption();
                this.isRedeemed = result.isRedeemed;
                
                if (this.isRedeemed) {
                    window.location.href = '{{ route('qrcode.redeemed', ['id' => $qrCodeId]) }}';
                } else {
                    setTimeout(() => this.checkRedemption(), this.pollInterval);
                }
            } catch (error) {
                console.error('Error checking redemption:', error);
                setTimeout(() => this.checkRedemption(), this.pollInterval);
            }
        }
    }"
    x-init="
        if ($wire.isValid) {
            checkRedemption();
            if ($wire.expirationTime) {
                const remainingTime = $wire.expirationTime - Date.now();
                if (remainingTime > 0) {
                    setTimeout(() => showExpiredMessage = true, remainingTime);
                } else {
                    showExpiredMessage = true;
                }
            }
        }
    "
    class="qr-code-display"
>
    @if($isValid)
        <p class="text-md mb-6 text-justify">{{ __('qrcode.instructions') }}</p>
        <div class="qr-code-container bg-white p-4 rounded-lg shadow-md">
            <img src="data:image/svg+xml;base64,{{ $qrCodeImage }}" alt="QR Code" class="mx-auto">
        </div>
    @else
        <p class="text-center text-error">{{__('qrcode.qr_code_expired')}}</p>
    @endif
</div>