@props(['label'])
<th {{ $attributes->merge(['class' => 'px-2 py-3 text-sm font-semibold text-white']) }}>
    {{ $label }}
</th>
