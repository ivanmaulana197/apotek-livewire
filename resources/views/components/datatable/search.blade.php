@props(['placeholder_search'])

<x-form.input wire:model.live.debounce300='search' placeholder="{{ $placeholder_search }}" inputGroup="left" rounded='full'
              {{ $attributes->merge(['class' => '']) }}>
    <x-slot:labelInputGroup>
        <i class="iconify tabler--search text-lg"></i>
        {{ $slot }}
    </x-slot:labelInputGroup>
</x-form.input>
