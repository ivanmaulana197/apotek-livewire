@props(['nama', 'placeholder', 'singleDatePicker' => false, 'errorName' => null, 'select_date','ranges'=>true])

<div>
    <div class="relative w-full">
        <!-- Icon di awal (Date) -->
        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
            <i class="ti ti-calendar"></i>
        </span>

        {{-- <input data-datepicker="true" type="text" id="{{ $nama }}" placeholder="{{ $placeholder ?? 'Pilih Tanggal' }}" {{ $attributes->merge(['class' => 'pl-10 pr-10 py-2 border rounded-md focus:ring focus:ring-blue-300 focus:border-blue-500 form-control']) }}> --}}
        <input type="text" id="{{ $nama }}" placeholder="{{ $placeholder ?? 'Pilih Tanggal' }}" data-ranges="{{ $ranges }}" data-datepicker="true" data-single="{{ $singleDatePicker ? 'true' : 'false' }}" @isset ($select_date) data-select_date='@json($select_date)' @endisset {{ $attributes->merge([
            'class' => 'pl-10 pr-10 py-2 border rounded-md focus:ring focus:ring-blue-300 focus:border-blue-500 form-control',
        ]) }}>

        <!-- Icon di akhir (Arrow Down) -->
        <span class="absolute inset-y-0 right-3 flex items-center text-gray-400">
            <i class="ti ti-chevron-down"></i>
        </span>
    </div>
    @if ($errorName)
        @error($errorName)
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    @endif

</div>
