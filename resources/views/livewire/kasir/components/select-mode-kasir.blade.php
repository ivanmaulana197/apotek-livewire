<x-form.select name="mode_kasir" :direction=false  :empty-option=false wire:model.live='mode_kasir'>
    <x-slot:custom-option>
        <option value="UMUM" {{ $mode_kasir == 'UMUM' ? 'selected' : '' }}>KASIR UMUM</option>
        <option value="RESEP" {{ $mode_kasir == 'RESEP' ? 'selected' : '' }}>KASIR RESEP</option>
    </x-slot:custom-option>
</x-form.select>
