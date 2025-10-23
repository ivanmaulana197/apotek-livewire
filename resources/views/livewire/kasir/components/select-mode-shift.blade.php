<x-form.select name="mode_shift" :direction=false :empty-option=false wire:model.live='mode_shift' error-name="mode_shift">
    <x-slot:custom-option>
        <option value="PAGI" {{ $mode_shift == 'PAGI' ? 'selected' : '' }}>PAGI</option>
        <option value="MALAM" {{ $mode_shift == 'MALAM' ? 'selected' : '' }}>MALAM</option>
    </x-slot:custom-option>
</x-form.select>
