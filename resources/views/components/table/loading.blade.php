@props(['colspan' => 1])

<tr wire:loading.class="table-row" wire:loading.remove.class="hidden" class="hidden">
    <td colspan="{{ $colspan }}" {{ $attributes->merge(['class' => 'text-center p-3']) }}>
        <span class="iconify eos-icons--loading"></span> Loading..
    </td>
</tr>

{{-- <tr wire:loading class="absolute inset-0 z-10 bg-gray-700/90 ">
    <td colspan="100%" class="absolute inset-0 space-x-2 flex items-center justify-center">
        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        <span>Memproses data...</span>
    </td>
</tr> --}}
