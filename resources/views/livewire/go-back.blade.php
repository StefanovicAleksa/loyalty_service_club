<div>
    <button 
        type="button"
        class="btn btn-circle {{ $class }}"
        x-data
        @click="window.history.back()"
    >
        <x-icon name="o-arrow-left" />
        {{ $label }}
    </button>
</div>