@props(['number','active'=>false])
@php
    $active = $active ? 'active': '';
@endphp
<button type="button" 
    {{ $attributes->merge(['class'=>" hs-tab-active:border-b-primary hs-tab-active:text-primary hs-tab-active:text-bold hs-tab-active:bg-lightprimary relative  min-w-0 flex-1 bg-white first:border-s-0 border-s border-b-4 border-gray-200 py-4 px-4 text-gray-500 hover:text-secondary hover:text-bold dark:hover:text-secondary dark:hover:text-bold text-sm font-medium text-center overflow-hidden hover:bg-lightsecondary dark:hover:bg-lightsecondary disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-l-neutral-700 dark:border-b-neutral-700 dark:text-neutral-400 $active" ]) }}
        id="bar-with-underline-item-{{ $number }}" 
        aria-selected="true" 
        data-hs-tab="#bar-with-underline-{{ $number }}" 
        aria-controls="bar-with-underline-{{ $number }}" role="tab">
    {{ $slot }}
</button>