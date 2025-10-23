@props(['number','active'=>false])
@php
    $active = $active ? 'active': '';
@endphp
<button type="button"
    {{ $attributes->merge(['class'=> "$active
    hs-tab-active:bg-white hs-tab-active:border-b-transparent hs-tab-active:text-primary hs-tab-active:border-gray-400
    underline underline-offset-[5px]
    dark:hs-tab-active:bg-darkprimary  dark:hs-tab-active:border-b-gray-800
    -mb-px py-3 px-4 inline-flex items-center gap-x-2
    bg-gray-100 text-sm font-medium text-center
    border border-gray-200 text-gray-500 rounded-t-lg
    hover:text-primary focus:outline-hidden focus:text-primary
    disabled:opacity-50 disabled:pointer-events-none
    dark:bg-darkprimary dark:border-gray-600  dark:text-neutral-400 dark:hover:text-primary dark:focus:text-primary "]) }}
        id="card-type-tab-item-{{ $number }}"
        aria-selected="true"
        data-hs-tab="#card-type-tab-{{ $number }}"
        aria-controls="card-type-tab-{{ $number }}" role="tab">

    
        Tab 1
    {{ $slot }}
</button>
