<div class="flex items-center justify-center h-screen max-w-md md:mx-auto mx-5">
    <form wire:submit.prevent="submit" class="w-full">
        {{ $this->form }}

        <button class="filament-button filament-button-size-md bg-amber-600 px-4 py-2 rounded mt-4" type="submit">
            Submit
        </button>
    </form>
</div>
