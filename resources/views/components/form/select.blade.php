@props([
    'name',
    'required' => false,
    'label',
    'customOption',
    'dataOption',
    'optionValue' => 'id',
    'optionLabel' => 'nama',
    'addValue' => false,
    'mainSelect' => null,
    'idMainSelect',
    'placeholder',
    'dropdownInput' => true,
    'emptyOption' => true,
    'itemSelected' => null,
    'errorName' => null,
    'direction' => true,
    'dropdownParent' => 'body',
])

@php
    if (!isset($labelButton)) {
        $labelButton = '+';
    }
@endphp

<div class="grid grid-cols-12" x-init="if (window.select{{ $name }}) {
    // Jika sudah ada instance, tidak perlu menginisialisasi ulang
    deleteTom()
    initialTom()
    return;
}

function initialTom() {
    window.select{{ $name }} = new TomSelect('#{{ $name }}-select', {
        //   maxOptions: 10,

        @if (!$emptyOption) allowEmptyOption: false, @endif
        @if ($direction) sortField: {
               field: 'text',
               direction: 'asc'
         }, @endif
        dropdownParent: '{{ $dropdownParent }}',
        plugins: [
            @if ($dropdownInput) 'dropdown_input', @endif
            @if ($emptyOption) 'remove_button', @else 'no_backspace_delete', @endif

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

$wire.on('selected-{{ $name }}', ({ selected }) => {
    select{{ $name }}.setValue(selected);
})

@if($addValue || $mainSelect)
Livewire.on('{{ $mainSelect ?? $name }}-created', (event) => {
    select{{ $name }}.addOption({
        value: event.{{ $mainSelect ?? $name }}.{{ $optionValue }},
        text: event.{{ $mainSelect ?? $name }}.{{ $optionLabel }}
    })
})
@endif
Livewire.on('update_{{ $name }}', ({ data, selected }) => {
    if (!window.select{{ $name }}) {
        initialTom()
    }

    if (data) {
        select{{ $name }}.clear();
        select{{ $name }}.clearOptions();
        select{{ $name }}.addOptions(data);
        select{{ $name }}.setValue(selected);
    } else {
        select{{ $name }}.clear();
    }
})">

    @isset($label)
        <x-form.label label="{{ $label }}" />
    @endisset

    @php
        if (
            !auth()
                ->user()
                ->can($name . '.create')
        ) {
            $addValue = false;
        }
    @endphp

    <div class="{{ $addValue ? 'col-span-10' : 'col-span-12' }}">
        <div wire:ignore wire:key="select-{{ $name }}">
            <select id="{{ $name }}-select" {{ $attributes->merge(['class' => 'rounded-md']) }} {{-- class="py-2 px-3 pe-6 border block w-full border-gray-200 shadow-sm text-sm rounded-lg focus:border-primary focus:ring-blue-500 form-control" --}}>
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
    </div>

    @if ($addValue)
        <div class="ms-2 col-span-2 flex items-center">
            <x-modal.button name="{{ $name }}" label="{{ $labelButton }}" />
        </div>
    @endif
    @if ($errorName)

        @if (str_contains($errorName, '*'))
            @php
                $baseErrorName = explode('.*', $errorName)[0];
            @endphp
            @error($baseErrorName)
                <div class="col-span-12">
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                </div>
            @enderror
        @endif

        @error($errorName)
            <div class="col-span-12">
                <span class="text-red-500 text-sm">{{ $message }}</span>
            </div>
        @enderror

    @endif

</div>
