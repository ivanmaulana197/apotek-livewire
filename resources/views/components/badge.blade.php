@props(['message', 'status' => 'info', 'icon'])

{{-- <svg class='flex-shrink-0 w-3 h-3' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'
       fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>
       <path d='m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z' />
       <path d='M12 9v4' />
       <path d='M12 17h.01' />
   </svg> --}}
@php
    if (!isset($icon)) {
        $icon = "solar:info-circle-bold";
    }
    
@endphp

<span {{ $attributes->merge(['class' => "mt-1 py-1 px-1.5 inline-flex flex-row flex-wrap gap-1 text-xs font-medium bg-light$status text-$status rounded-lg dark:bg-dark$status dark:text-$status"]) }}>

    @if ($icon instanceof \Illuminate\View\ComponentSlot)
        <div {{ $icon->attributes->merge(['class' => '']) }}>
            {{ $icon }}
        </div>
    @elseif ($icon)
        @if($icon != "")
            <iconify-icon icon="{{ $icon }}" class="mt-0.5"></iconify-icon>
        @endif
    @endif

    {{ $message }}
</span>
