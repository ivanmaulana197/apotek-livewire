@props([
    'color' => 'primary',
    'href' => '#',
    'icon',
    'title',
    'menuLevel' => false,
    'name',
])

@php
    $colorList = ['primary', 'error', 'gray', 'indigo', 'info', 'secondary', 'success', 'warning'];
    if (in_array($color, $colorList)) {
        $bg = "light$color";
    } else {
        $bg = "$color-700/20";
        $color = "$color-500";
    }
    // Memeriksa apakah href saat ini sama dengan route yang aktif

    if ($menuLevel) {
        $isActive = request()->is("$name*") ? true : false;

    } else {
        // $isActive = url()->current() == $href ? true : false;
        $route = ltrim(parse_url($href, PHP_URL_PATH),'/');
        if(!$route){
            $isActive = url()->current() == $href ? true : false;
        }else{
            $isActive = request()->is("$route*") ? true : false;
        }
    }
    $active = $isActive ? 'active' : '';
    // active sidebar-link before:bg-lightindigo dark:before:bg-darkindigo text-indigo dark:text-indigo
@endphp


@if ($menuLevel)
    <li class="hs-accordion sidebar-item {{ $active }}" id="hs-heading-{{ $name }}">
        <a class="cursor-pointer hs-accordion-toggle sidebar-link before:bg-{{ $bg }} {{ $active }}"  aria-expanded="{{ $isActive?'true':'false' }}" aria-controls="hs-collapse-{{ $name }}">
            <div class="w-full h-full gap-1 flex items-center relative hover:text-{{ $color }} {{ $isActive ? "text-$color dark:text-$color" : '' }} ">
               <div class="p-2 flex items-center">
                    <span class="iconify {{ $icon }} text-xl"></span>
                </div>
                <span class="flex-shrink-0 hide-menu ">{{ $title }}</span>
                <span class="hide-menu ms-auto">
                    <i class="text-lg leading-tight ti ti-chevron-right me-4 hs-accordion-active:hidden "></i>
                    <i class="relative z-10 hidden text-lg ti ti-chevron-up ms-auto hs-accordion-active:block me-4"></i>
                </span>
            </div>
        </a>
        @if ($slot->isNotEmpty())
            <div id="hs-collapse-{{ $name }}" class="hs-accordion-content transition-[height] duration-300 {{ $isActive ? 'active-menu h-full' : '' }}" aria-labelledby="hs-heading-{{ $name }}" style="{{ $isActive? '':'display: none;' }}">
                <ul class="hs-accordion-group ">
                    {{ $slot }}
                </ul>
            </div>
        @endif
    </li>
@else
    <li class="sidebar-item">
        <a class="sidebar-link before:bg-{{ $bg }} {{ $active }}"
            href="{{ $href }}"
            wire:navigate>
            <div class="w-full h-full gap-1 flex items-center relative hover:text-{{ $color }} {{ $isActive ? "text-$color dark:text-$color" : '' }} ">
                <div class="p-2 flex items-center">
                    <span class="iconify {{ $icon }} text-xl"></span>
                </div>
                <span class="flex-shrink-0 hide-menu ">{{ $title }}</span>
                @if ($menuLevel)
                    <span class="hide-menu ms-auto">
                        <i class="text-lg leading-tight ti ti-chevron-right me-4 hs-accordion-active:hidden "></i>
                        <i class="relative z-10 hidden text-lg ti ti-chevron-up ms-auto hs-accordion-active:block me-4"></i>
                    </span>
                @endif
            </div>
        </a>
    </li>
@endif
