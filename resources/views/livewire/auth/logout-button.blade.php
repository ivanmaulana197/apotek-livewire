@php
    if ($tipe == 'sidebar') {
        $class = 'text-link  text-primary hs-tooltip [--placement:auto] hs-tooltip-toggle';
    } elseif ($tipe == 'navbar') {
        $class = 'w-full btn hs-tooltip [--placement:auto] hs-tooltip-toggle';
    }
@endphp

<button wire:click='logout' class="{{ $class }}">
    @if ($tipe == 'sidebar')
        <span class="iconify solar--logout-line-duotone text-3xl"></span>
    @endif
    @if ($tipe == 'navbar')
        Log Out
    @endif
    <span class="tooltip hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible" role="tooltip">
        Logout
    </span>
</button>
