@props(['name', 'label', 'type' => 'text', 'placeholder', 'labelInputGroup', 'inputGroup', 'errorName' => null])

@php
    if (!isset($placeholder)) {
        $placeholder = "Masukan $label";
    }
    if (isset($inputGroup)) {
        if ($inputGroup == 'left') {
            $border = 'rounded-e-lg';
        } elseif ($inputGroup == 'right') {
            $border = 'rounded-s-lg';
        }
    }

@endphp

<div class="grid grid-cols-12 mb-2">
    <div class="col-span-12 md:col-span-2">
        <x-form.label label="{{ $label }}" />
    </div>
    {{-- <div class="col-span-12 md:col-span-10 flex items-center justify-center"> --}}
    <div class="col-span-12 md:col-span-10">
        @if ($type == 'textarea')
            <textarea {{ $attributes->merge(['class' => 'textarea textarea-bordered h-28 w-full  shadow-sm text-sm rounded-lg focus:border-primary focus:ring-primary form-control dark:text-white']) }} placeholder="{{ $placeholder }}"></textarea>
        @else
            @isset($inputGroup)
                <div class="flex rounded-lg">
                    @if ($inputGroup == 'left' || $inputGroup == 'both')
                        <span class="px-4 inline-flex items-center min-w-fit rounded-s-lg border border-s-0 border-bordergray bg-gray-100 text-base text-gray-500 dark:bg-gray-700 dark:border-gray-700 dark:text-gray-400">
                            {{ $labelInputGroup }}
                        </span>
                    @endif
                    <input type="{{ $type }}" placeholder="{{ $placeholder }}" {{ $attributes->merge(['class' => "py-2 px-3 pe-6 block w-full shadow-sm text-sm focus:border-primary focus:ring-primary form-control rounded-none $border  disabled:opacity-50 disabled:pointer-events-none"]) }} />
                    @if ($inputGroup == 'right' || $inputGroup == 'both')
                        <span class="px-4 inline-flex items-center min-w-fit rounded-e-lg border border-s-0 border-bordergray bg-gray-100 text-xs text-gray-500 dark:bg-gray-700 dark:border-gray-700 dark:text-gray-400">
                            {{ $labelInputGroup }}
                        </span>
                    @endif
                </div>
            @else
                <input type="{{ $type }}" placeholder="{{ $placeholder }}" class="py-2 px-3 pe-6 block w-full shadow-sm text-sm rounded-lg focus:border-primary focus:ring-primary form-control disabled:opacity-50 disabled:pointer-events-none dark:text-white" {{ $attributes }} />
            @endisset
        @endif

        @if ($errorName)
            @error($errorName)
                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        @endif
    </div>
</div>
