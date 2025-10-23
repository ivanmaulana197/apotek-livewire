@props([
    'name',
    'required' => false,
    'label',
    'customOption',
    'dataOption',
    'optionValue',
    'optionLabel',
    'addValue' => false,
    'mainSelect' => null,
    'idMainSelect',
    'placeholder',
    'dropdownInput' => true,
    'emptyOption' => true,
    'itemSelected' => null,
    'errorName' => null,
    'direction' => true,
])

@php
    if (!isset($labelButton)) {
        $labelButton = '+';
    }
@endphp

<div class="grid grid-cols-12 gap-2 mb-3" x-init="if (window.select{{ $name }}) {
    // Jika sudah ada instance, tidak perlu menginisialisasi ulang
    deleteTom()
    initialTom()
    return;
}

function initialTom() {
    window.select{{ $name }} = new TomSelect('#{{ $name }}-select', {
        //   maxOptions: 10,
        // allowEmptyOption: true,
        @if ($direction) sortField: {
            field: 'text',
            direction: 'asc'
      }, @endif

        plugins: [
            @if ($dropdownInput) 'dropdown_input', @endif
            @if ($emptyOption) 'remove_button' @endif
        ],
    });
}

function deleteTom() {
    window.select{{ $name }}.destroy();
    delete window.select{{ $name }};
}

initialTom()


$wire.on('clear-select-{{ $name }}', () => { //from local event
    select{{ $name }}.setValue('');
})

@if($addValue || $mainSelect)
Livewire.on('{{ $mainSelect ?? $name }}-created', (event) => {
    select{{ $name }}.addOption({
        value: event.{{ $mainSelect ?? $name }}.{{ $optionValue }},
        text: event.{{ $mainSelect ?? $name }}.{{ $optionLabel }}
    })
})
@endif
Livewire.on('selected_{{ $name }}', ({ selected }) => {
    console.log(selected);

    select{{ $name }}.setValue(selected);
});
Livewire.on('update_{{ $name }}', ({ data, selected }) => {
    if (!window.select{{ $name }}) {
        initialTom()
    }
    console.log(data);

    if (data) {
        console.log('{{ $name }} ada data');

        select{{ $name }}.clear();
        select{{ $name }}.clearOptions();
        select{{ $name }}.addOptions(data);
        select{{ $name }}.setValue(selected);
    } else {
        select{{ $name }}.clear();

    }
})">
    <div class="col-span-12 md:col-span-2">
        <label for="{{ $name }}" class=" text-sm font-semibold flex items-center h-full text-gray-800 dark:text-neutral-200">
            {{ $label }}
        </label>
    </div>
    <!-- End Col -->

    <div class="{{ $addValue ? 'col-span-10 md:col-span-8' : 'col-span-12 md:col-span-10' }}">
        <div wire:ignore>
            <select id="{{ $name }}-select" {{ $attributes->merge(['class' => 'rounded-md']) }}>
                {{-- class="py-2 px-3 pe-6 border block w-full border-gray-200 shadow-sm text-sm rounded-lg focus:border-primary focus:ring-blue-500 form-control" > --}}
                @if ($emptyOption)
                    <option value="" selected>Pilih {{ $placeholder ?? $name }} ...</option>
                @endif
                @isset($dataOption)
                    @foreach ($dataOption as $item)
                        @if (is_array($itemSelected))
                            <option value="{{ $item[$optionValue] }}" {{ in_array($item[$optionValue], $itemSelected) ? 'selected' : '' }}>
                                {{ $item[$optionLabel] }}</option>
                        @else
                            <option value="{{ $item[$optionValue] }}" {{ $item[$optionValue] == $itemSelected ? 'selected' : '' }}>
                                {{ $item[$optionLabel] }}</option>
                        @endif
                    @endforeach
                @else
                    {{ $customOption }}
                @endisset
            </select>
        </div>

        @if ($errorName)
            @error($errorName)
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        @endif

    </div>

    @if ($addValue)
        <div class="col-span-2 flex items-center">
            <x-modal.button name="{{ $name }}" label="{{ $labelButton }}" />
        </div>
    @endif

</div>
