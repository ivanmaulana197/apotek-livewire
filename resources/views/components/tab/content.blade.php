@props(['number','active' => false])

@php
    $class = $active ? 'block':'hidden';
@endphp


<div id="bar-with-underline-{{ $number }}" role="tabpanel" aria-labelledby="bar-with-underline-item-{{ $number }}" {{ $attributes->merge(['class'=>"$class"]) }}>
    {{ $slot }}
</div>