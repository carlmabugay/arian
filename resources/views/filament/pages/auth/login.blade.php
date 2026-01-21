<x-filament-panels::page.simple>
    <form
        wire:submit.prevent="authenticate"
        id="form"
        class="space-y-6"
    >
        {{ $this->form }}

        <x-filament::button
            type="submit"
            size="lg"
            color="primary"
            icon="heroicon-m-arrow-right-end-on-rectangle"
            class="w-full"
            wire:loading.attr="disabled"
            wire:target="authenticate"
        >
            Login

        </x-filament::button>
    </form>

    <div class="mt-6 text-center text-xs text-gray-500 dark:text-white">
        © {{ now()->year }} Arian
    </div>
</x-filament-panels::page.simple>
