@props(['colspan' => 1, 'content' => 'Tidak Ada Data'])

<x-table.tr>
    <td colspan="{{ $colspan }}" {{ $attributes->merge(['class' => 'p-2 text-sm font-semibold text-center text-dark dark:text-white']) }}>
        {{ $content }}
    </td>
    {{ $slot }}
</x-table.tr>
