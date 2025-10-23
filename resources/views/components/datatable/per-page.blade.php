@props(['nama_table', 'label' => false, 'perPage' => '5'])
<x-form.select name="perPage_{{ $nama_table }}" :empty-option=false :direction=false wire:model.live='perPage'
               {{ $attributes->merge(['class' => 'min-w-14']) }}>
    @if ($label)
        <x-slot:label>
            Show :
        </x-slot:label>
    @endif
    <x-slot:custom-option>
        <option value="5" {{ $perPage == '5' ? 'selected' : '' }}>5</option>
        <option value="10" {{ $perPage == '10' ? 'selected' : '' }}>10</option>
        <option value="25" {{ $perPage == '25' ? 'selected' : '' }}>25</option>
        <option value="50" {{ $perPage == '50' ? 'selected' : '' }}>50</option>
    </x-slot:custom-option>
</x-form.select>
