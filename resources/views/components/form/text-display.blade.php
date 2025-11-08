@props(['value', 'label', 'labelInputGroup', 'inputGroup', 'rounded' => 'lg', 'grid' => false, 'addValue' => false, 'customButton', 'labelButton'])

@php
    if (!isset($labelButton)) {
        $labelButton = '+';
    }
@endphp

@php
    if (isset($inputGroup)) {
        if ($inputGroup == 'left') {
            $border = "rounded-e-{$rounded}";
        } elseif ($inputGroup == 'right') {
            $border = "rounded-s-{$rounded}";
        }
    }
@endphp

<div class="grid grid-cols-12">
    @isset($label)
        <x-form.label label="{{ $label }}" :$grid />
    @endisset
    <div class="{{ $addValue ? 'col-span-10' : 'col-span-12' }} {{ $grid ? 'sm:col-span-10 md:col-span-8' : '' }}">

        @isset($inputGroup)
            <div class="flex ">
                @if ($inputGroup == 'left' || $inputGroup == 'both')
                    <span
                          class="px-3 py-1.5 inline-flex items-center rounded-s-{{ $rounded }} border rounded-e-0 border-e-0 border-bordergray text-xs bg-primary text-white  dark:bg-lightprimary dark:text-primary dark:border-slate-700">
                        {{ $labelInputGroup }}
                    </span>
                @endif

                <span
                      {{ $attributes->merge(['class' => "py-1.5 px-3 pe-6 block flex-1 min-w-0 w-full rounded-none $border border text-gray-900  text-xs border-bordergray  dark:border-slate-700 dark:text-gray-400"]) }}>
                    {{ $value ?? ($slot ?? '-') }}
                </span>
                @if ($inputGroup == 'right' || $inputGroup == 'both')
                    {{-- <span class="px-4 py-2 inline-flex items-center min-w-fit rounded-e-{{ $rounded }} border border-s-0 border-bordergray text-xs bg-primary text-white dark:bg-lightprimary dark:text-primary hover:bg-primaryemphasis dark:hover:bg-primary dark:hover:text-white"> --}}
                    <span
                          class="px-3 py-1.5 inline-flex items-center rounded-e-{{ $rounded }} border rounded-s-0 border-s-0 border-bordergray text-xs bg-primary text-white  dark:bg-lightprimary dark:text-primary dark:border-slate-700">
                        {{ $labelInputGroup }}
                    </span>
                @endif
            </div>
        @else
            <span
                  {{ $attributes->merge(['class' => "py-1.5 px-3 pe-6 block w-full shadow-sm text-xs rounded-$rounded focus:border-primary focus:ring-primary form-control disabled:opacity-50 disabled:pointer-events-none"]) }}>
                {{ $value ?? '-' }}
            </span>
        @endisset

    </div>
    @if ($addValue)
        <div class="ms-2 col-span-2 flex items-center">
            @isset($customButton)
                {{ $customButton }}
            @else
                <x-modal.button name="{{ $name }}" label="{{ $labelButton }}" />
            @endisset
        </div>
    @endif
</div>
