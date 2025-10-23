@props(['number', 'active' => false])

@php
    $active = $active ? 'active' : '';
@endphp

<button type="button"
        class="
            {{ $active }}
             me-3
            w-full
            hs-tab-active:bg-primary/30 hs-tab-active:font-bold hs-tab-active:text-primary hs-tab-active:border-gray-400
            underline underline-offset-[5px]
            dark:hs-tab-active:bg-darkprimary
            -mb-px py-3 px-4 inline-flex items-center gap-x-2
            bg-gray-100 text-sm font-medium text-center
            border border-gray-200 text-gray-500 rounded-xl
            hover:text-primary focus:outline-hidden focus:text-primary
            disabled:opacity-50 disabled:pointer-events-none
            dark:bg-darkprimary dark:border-gray-600  dark:text-neutral-400 dark:hover:text-primary dark:focus:text-primary"
        id="vertical-tab-with-border-item-{{ $number }}" aria-selected="true" data-hs-tab="#vertical-tab-with-border-{{ $number }}"
        aria-controls="vertical-tab-with-border-{{ $number }}" role="tab">
    {{ $slot }}
</button>
