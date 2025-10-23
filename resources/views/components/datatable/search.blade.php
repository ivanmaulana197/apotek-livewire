@props(['placeholder_search'])

<x-form.input wire:model.live.debounce300='search' placeholder="{{ $placeholder_search }}" inputGroup="left" rounded='full' {{ $attributes->merge(['class' => '']) }}>
    <x-slot:labelInputGroup>
        <iconify-icon icon="wpf:search" class="text-lg">
        </iconify-icon>
        {{ $slot }}
    </x-slot:labelInputGroup>
</x-form.input>
