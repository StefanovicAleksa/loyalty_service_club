<div
    x-data="{
        inputs: [],
        value: '{{ $value }}',
        init() {
            this.inputs = Array({{ $size }}).fill('').map((_, i) => this.value[i] || '');
            this.$watch('inputs', () => {
                this.value = this.inputs.join('');
                this.$dispatch('input', this.value);
            });
            document.getElementById('pin{{ $uuid }}').addEventListener('paste', (e) => {
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                for (let i = 0; i < {{ $size }}; i++) {
                    this.inputs[i] = paste[i] || '';
                }
                e.preventDefault();
            });
        },
        next(el) {
            if (el.value.length === 0) {
                return;
            }
            if (el.nextElementSibling) {
                el.nextElementSibling.focus();
                el.nextElementSibling.select();
            }
        },
        remove(el, i) {
            this.inputs[i] = '';
            if (el.previousElementSibling) {
                el.previousElementSibling.focus();
                el.previousElementSibling.select();
            }
        }
    }"
>
    <div class="flex gap-3" id="pin{{ $uuid }}">
        @foreach(range(0, $size - 1) as $i)
            <input
                id="{{ $uuid }}-pin-{{ $i }}"
                type="text"
                class="input input-primary !w-12 font-black text-right text-lg {{ $class }}"
                maxlength="1"
                x-model="inputs[{{ $i }}]"
                @keydown.space.prevent
                @keydown.backspace.prevent="remove($event.target, {{ $i }})"
                @input="next($event.target)"
                @if($numeric)
                    inputmode="numeric"
                    x-mask="9"
                @endif
                required
            />
        @endforeach
    </div>
   
    <x-mary-input
        :name="$name"
        x-model="value"
        type="hidden"
        :error="$errors->first('otp')"
    />
</div>