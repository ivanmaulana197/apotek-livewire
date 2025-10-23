@props(['coba' => null, 'label', 'errorName' => null, 'type' => 'text', 'placeholder','labelLeftInputGroup','LabelRightInputGroup', 'labelInputGroup', 'inputGroup', 'rounded' => 'lg'])

@php
    if (!isset($placeholder)) {
        $placeholder = "Masukan $label";
    }
    if (isset($inputGroup)) {
        if ($inputGroup == 'left') {
            $border = "rounded-e-{$rounded}";
            $labelLeftInputGroup = $labelInputGroup;
        } elseif ($inputGroup == 'right') {
            $border = "rounded-s-{$rounded}";
            $labelRightInputGroup = $labelInputGroup;
        }elseif($inputGroup == 'both'){
            $border = "rounded-{$rounded}";
        }
    }
@endphp

<div class="grid grid-cols-12">
    @isset($label)
        <x-form.label label="{{ $label }}" />
    @endisset
    <div class="col-span-12">
        @if ($type == 'textarea')
            <textarea {{ $attributes->merge(['class' => "textarea textarea-bordered h-28 w-full  shadow-sm text-sm rounded-{$rounded} focus:border-primary focus:ring-primary form-control dark:text-slate-300"]) }}
                      placeholder="{{ $placeholder }}"></textarea>
        @else
            @isset($inputGroup)
                <div class="flex rounded-{{ $rounded }}">
                    @if ($inputGroup == 'left' || $inputGroup == 'both')
                        <span
                              class="px-3 py-2 inline-flex items-center rounded-s-{{ $rounded }} border rounded-e-none border-e-0 border-bordergray text-xs bg-primary text-white  dark:bg-darkprimary dark:text-primary dark:border-slate-700">
                            {{ $labelLeftInputGroup }}
                        </span>
                    @endif
                    <input type="{{ $type }}" placeholder="{{ $placeholder }}" @input="$el.value === '' ? null : $el.value"
                           {{ $attributes->merge(['class' => "py-2 px-3 pe-6 block w-full shadow-sm text-xs focus:border-primary focus:ring-primary form-control rounded-none $border  disabled:opacity-50 disabled:pointer-events-none dark:text-slate-300"]) }} />
                    @if ($inputGroup == 'right' || $inputGroup == 'both')
                        <span
                              class="px-3 py-2 inline-flex items-center rounded-e-{{ $rounded }} border rounded-s-0 border-s-0 border-bordergray text-xs bg-primary text-white  dark:bg-darkprimary dark:text-primary dark:border-slate-700">
                            {{ $labelRightInputGroup }}
                        </span>
                    @endif
                </div>
            @else
                <div class="relative">
                    <input type="{{ $type }}" placeholder="{{ $placeholder }}" @input="$el.value === '' ? null : $el.value"
                           {{ $attributes->merge(['class' =>"py-2 px-3 pe-6 block w-full shadow-sm text-sm rounded-$rounded focus:border-primary focus:ring-primary form-control disabled:opacity-50 disabled:pointer-events-none dark:text-slate-300"]) }}
                            />
                    {{-- @if ($type == 'password')
                        <button type="button" data-hs-toggle-password='{"target": ["#password"]}' class="absolute inset-y-0 end-0 flex items-center z-20 px-3 cursor-pointer text-gray-400 rounded-e-md focus:outline-hidden focus:text-blue-600 dark:text-neutral-600 dark:focus:text-blue-500">
                            <i class="ti ti-eye-off text-xl hs-password-active:hidden"></i>
                            <i class="ti ti-eye text-xl hidden hs-password-active:block"></i>
                        </button>
                    @endif --}}
                </div>
            @endisset
        @endif

        {{-- {{ $slot }} --}}

        @if ($errorName)
            <x-error-bag errorName="`{!! $errorName !!}`" />
        @endif


    </div>
</div>
