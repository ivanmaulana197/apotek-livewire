@props(['tooltip' => null, 'color' => 'primary', 'type' => 'button', 'newTab' => false])

<{{ $type == 'href' ? 'a' : 'button' }} {{ $attributes->merge([
    'type' => $type,
    'class' => " p-1 m-[2px] inline-flex justify-center items-center text-lg font-medium rounded-xs border border-transparent bg-$color hover:bg-{$color}emphasis text-white hs-tooltip",
]) }}
                                        target="{{ $newTab ? '_blank' : '' }}">

    {{ $slot }}

    {{-- <i class="iconify tabler--edit-circle"></i> --}}
    {{-- <iconify-icon icon="fa-solid:edit" class="block"></iconify-icon> --}}

    @if ($tooltip instanceof \Illuminate\View\ComponentSlot)
        <span
              {{ $tooltip->attributes->merge(['class' => 'hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded shadow-sm dark:bg-slate-700']) }}>
            {{ $tooltip }}
        </span>
    @elseif ($tooltip)
        <span
              class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded shadow-sm dark:bg-slate-700">
            {{ $tooltip }}
        </span>
    @endif
    </{{ $type == 'href' ? 'a' : 'button' }}>
