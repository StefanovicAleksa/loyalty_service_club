<div
    x-data="{
        formattedPhone: '',
        rawPhone: '',
        digitNumber: parseInt($el.getAttribute('data-digit-number')),
        formatPattern: new RegExp($el.getAttribute('data-format-pattern')),
        countryCode: '+' + $el.getAttribute('data-country-code'),
        init() {
            this.setPhoneValues('');
        },
        formatPhone() {
            this.setPhoneValues(this.formattedPhone);
        },
        setPhoneValues(value) {
            this.rawPhone = value.replace(/\D/g, '').slice(0, this.digitNumber);
            this.formattedPhone = this.formatPhoneNumber(this.rawPhone);
            this.$refs.rawInput.value = this.countryCode + this.rawPhone; // Update hidden input with country code and raw phone
        },
        formatPhoneNumber(value) {
            return value.replace(this.formatPattern, '$1 $2 $3').trim();
        }
    }"
    data-digit-number="{{ $digitNumber }}"
    data-format-pattern="{{ $formatPattern }}"
    data-country-code="{{ $countryCode }}"
    wire:ignore
>
    <x-input
        :label="$label"
        :placeholder="$placeholder"
        :error-field="$errorField"
        prefix="+{{ $countryCode }}"
        x-model="formattedPhone"
        x-on:blur="formatPhone"
        name="formattedPhone" 
        required
    />
    
    <input
        type="hidden"
        x-ref="rawInput"
        name="phone"
        :value="countryCode + rawPhone"
    />
</div>