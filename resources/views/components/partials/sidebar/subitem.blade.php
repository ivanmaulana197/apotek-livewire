@props([
    'href' => '#',
    'name',
    'color' => 'primary',
])

@php
    $colorList = ['primary', 'error', 'gray', 'indigo', 'info', 'secondary', 'success', 'warning'];
    if (in_array($color, $colorList)) {
        $bg = "light$color";
    } else {
        $bg = "$color-700/20";
        $color = "$color-500";
    }
    $isActive = request()->is("$name*") ? true : false;
@endphp

<li class="dropdown-submenu">
    <a class="hs-accordion-toggle rounded-md ps-2 !py-1.5 textlink dark:opacity-70 hover:text-{{ $color }} hover:bg-{{ $bg }} {{ $isActive ? "active font-semibold text-$color bg-$bg" : '' }}"
       href="{{ $href }}" wire:navigate>
        <div class="flex justify-center items-center opacity-50 ps-2 {{ $isActive ? 'active opacity-100' : '' }}">
            {{-- <iconify-icon icon="solar:map-arrow-right-bold-duotone" class="text-md {{ $isActive ? "text-$color" : '' }}"></iconify-icon> --}}
            <span class="iconify solar--map-arrow-right-bold-duotone text-sm {{ $isActive ? "text-$color" : '' }}"></span>
        </div>
        <span class="hide-menu {{ $isActive ? "text-$color" : '' }}">{{ $slot }}</span>
    </a>
</li>
