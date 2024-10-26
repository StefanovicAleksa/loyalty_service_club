<div x-data="{
    showPassword: false,
    togglePassword() {
        this.showPassword = !this.showPassword;
    }
}">
    <x-input
        :label="$label"
        :placeholder="$placeholder"
        :icon="$icon"
        :error-field="$errorField"
        x-bind:type="showPassword ? 'text' : 'password'"
        wire:model.defer="password"
        autocomplete="new-password"
        name="{{$name}}"
        required
    >
        <x-slot name="append">
            <button 
                type="button" 
                class="p-2" 
                @click="togglePassword"
                x-bind:aria-label="showPassword ? 'Hide password' : 'Show password'"
            >
                <template x-if="!showPassword">
                    <x-icon name="o-eye" class="text-gray-400 hover:text-gray-600" />
                </template>
                <template x-if="showPassword">
                    <x-icon name="o-eye-slash" class="text-gray-400 hover:text-gray-600" />
                </template>
            </button>
        </x-slot>
    </x-input>
</div>
