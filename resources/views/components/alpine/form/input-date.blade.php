@props(['label', 'placeholder', 'singleDatePicker' => false, 'errorName' => null, 'ranges' => true, 'minDate' => false, 'parentEl' => 'body'])

<div x-data="{
    ranges: {{ json_encode($ranges ?? false) }},
    minDate: {{ json_encode($minDate) }} ? moment({{ json_encode($minDate) }}) : false,
    singleDatePicker: {{ json_encode($singleDatePicker) }},
    parentEl: {{ json_encode($parentEl) }},
}" x-init="">
    @isset($label)
        <x-form.label :label="$label" />
    @endisset
    <div class="relative w-full">

        <!-- Icon di awal (Date) -->
        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
            <span class="iconify tabler--calendar"></span>
        </span>

        <input x-init="initDateRangePicker({ el: $el, ranges: ranges, singleDatePicker: singleDatePicker, minDate: minDate, parentEl: parentEl })" autocomplete="off" type="text" placeholder="{{ $placeholder ?? 'Pilih Tanggal' }}"
               {{ $attributes->merge(['class' => 'pl-10 pr-10 py-2 border rounded-md focus:ring focus:ring-blue-300 focus:border-blue-500 form-control']) }}>

        <!-- Icon di akhir (Arrow Down) -->
        <span class="absolute inset-y-0 right-3 flex items-center text-gray-400">
            <span class="iconify tabler--chevron-down"></span>
        </span>
    </div>
    @if ($errorName)
        <x-error-bag errorName="`{!! $errorName !!}`" />
    @endif

</div>
