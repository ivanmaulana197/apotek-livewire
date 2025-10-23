@props([
    'modalName',
    'mainSelect' => null,
    'init' => true,
    'label',
    'customOption',
    'dataOption',
    'optionValue' => 'id',
    'optionLabel' => 'nama',
    'addValue' => false,
    'placeholder',
    'dropdownInput' => true,
    'emptyOption' => true,
    'errorName' => null,
    'direction' => true,
    'dropdownParent' => 'body',
])

@php
    if (!isset($labelButton)) {
        $labelButton = '+';
    }
@endphp

<div class="grid grid-cols-12" x-data="{

    mainSelect: @js($mainSelect),
    direction: @js($direction),
    optionValue: @js($optionValue),
    optionLabel: @js($optionLabel),
    dropdownParent: @js($dropdownParent),
    dropdownInput: @js($dropdownInput),
    emptyOption: @js($emptyOption),
}">
    @isset($label)
        <x-form.label label="{{ $label }}" />
    @endisset
    <div class="{{ $addValue ? 'col-span-10' : 'col-span-12' }}" x-data>
        <div wire:ignore>
            <select @if ($init) x-init="initTomSelect({ el: $el, mainSelect: mainSelect, optionValue: optionValue, optionLabel: optionLabel, direction: direction, dropdownParent: dropdownParent, dropdownInput: dropdownInput, emptyOption: emptyOption })" @endif
                    {{ $attributes->merge(['class' => 'rounded-md']) }}>
                @if ($emptyOption)
                    <option value="" selected>Pilih {{ $placeholder ?? 'opsi' }} ...</option>
                @endif
                @isset($dataOption)
                    @foreach ($dataOption as $item)
                        <option value="{{ $item[$optionValue] }}">
                            {{ $item[$optionLabel] }}</option>
                    @endforeach
                @else
                    {{ $customOption }}
                @endisset
            </select>
        </div>
    </div>

    @if ($addValue)
        <div class="flex items-center col-span-2 ms-2">
            <x-modal.button name="{{ $modalName }}" label="{{ $labelButton }}" />
        </div>
    @endif

    {{-- {{ $slot }} --}}

    @if ($errorName)
        <x-error-bag errorName="`{!! $errorName !!}`" />
    @endif

</div>
