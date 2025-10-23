@props(['colspan' => 1, 'content' => 'Tidak Ada Data'])

<tr>
    <td colspan="{{ $colspan }}" 
    {{ $attributes->merge(['class'=>'p-2 text-sm font-semibold text-center text-dark dark:text-white']) }}>
        {{ $content }}
    </td>
</tr>
