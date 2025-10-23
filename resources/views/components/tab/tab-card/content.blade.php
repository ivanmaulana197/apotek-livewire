@props(['number', 'active' => false])

@php
    $active = $active ? 'block' : 'hidden';
@endphp
<div {{ $attributes->merge(['class' => "$active"]) }} id="card-type-tab-{{ $number }}" role="tabpanel"
     aria-labelledby="card-type-tab-item-{{ $number }}">
    {{ $slot }}
</div>
